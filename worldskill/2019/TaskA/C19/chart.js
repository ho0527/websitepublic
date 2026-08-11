/* ==========================================================================
   C19 Chart from data
   讀取 death-rate-by-source-from-air-pollution.csv，將 1990～2017 年
   三個污染來源的死亡率畫成堆疊面積圖（純 HTML / CSS / JavaScript，未使用外部函式庫）
   ========================================================================== */

(function () {
	"use strict";

	const CSV_URL = "death-rate-by-source-from-air-pollution.csv";
	const SVG_NS = "http://www.w3.org/2000/svg";

	/* 三個資料欄位（由下往上堆疊）與對應顏色 */
	const SERIES = [
		{ key: "Outdoor particulate matter (deaths per 100,000)", name: "Outdoor particulate matter", color: "#6a92cb" },
		{ key: "Household pollution from solid fuels (deaths per 100,000)", name: "Household pollution from solid fuels", color: "#d9605a" },
		{ key: "Ozone pollution (deaths per 100,000)", name: "Ozone pollution", color: "#f2b552" }
	];

	/* 繪圖區尺寸與邊界 */
	const VIEW = { width: 960, height: 520 };
	const PAD = { top: 44, right: 200, bottom: 46, left: 62 };
	const PLOT = {
		width: VIEW.width - PAD.left - PAD.right,
		height: VIEW.height - PAD.top - PAD.bottom
	};

	const svg = document.getElementById("chart-svg");
	const tooltip = document.getElementById("tooltip");
	const legendBox = document.getElementById("legend");
	const scopeSelect = document.getElementById("scope");
	const scopeHint = document.getElementById("scope-hint");
	const titleText = document.getElementById("chart-title");

	/* 目前的顯示狀態 */
	const state = {
		rows: [],           // 解析後的 CSV 資料
		entities: [],       // 國家清單
		scope: "__ALL__",   // 目前選擇的範圍
		visible: SERIES.map(() => true)
	};

	/* ----------------------------------------------------------------------
	   CSV 解析（支援以雙引號包住、內含逗號的欄位）
	   ---------------------------------------------------------------------- */
	function parseCsv(text) {
		const lines = text.replace(/\r/g, "").split("\n").filter(line => line !== "");
		const header = splitCsvLine(lines[0]);
		return lines.slice(1).map(line => {
			const cells = splitCsvLine(line);
			const row = {};
			header.forEach((name, i) => { row[name] = cells[i]; });
			return row;
		});
	}

	function splitCsvLine(line) {
		const cells = [];
		let current = "";
		let inQuote = false;

		for (let i = 0; i < line.length; i += 1) {
			const ch = line[i];
			if (ch === "\"") {
				/* 連續兩個雙引號代表資料本身的雙引號 */
				if (inQuote && line[i + 1] === "\"") { current += "\""; i += 1; }
				else { inQuote = !inQuote; }
			} else if (ch === "," && !inQuote) {
				cells.push(current);
				current = "";
			} else {
				current += ch;
			}
		}
		cells.push(current);
		return cells;
	}

	/* ----------------------------------------------------------------------
	   依選擇的範圍彙整資料：回傳 [{ year, values: [下, 中, 上] }, ...]
	   __ALL__ 代表「所有國家的平均」，其餘為單一 Entity
	   ---------------------------------------------------------------------- */
	function aggregate(scope) {
		const buckets = new Map();   // year -> { sum: [], count: [] }

		state.rows.forEach(row => {
			if (scope === "__ALL__") {
				/* 只取有國碼且非世界彙總的列，避免區域與世界資料重複計算 */
				if (!row.Code || row.Code === "OWID_WRL") { return; }
			} else if (row.Entity !== scope) {
				return;
			}

			const year = Number(row.Year);
			if (!Number.isFinite(year)) { return; }

			if (!buckets.has(year)) {
				buckets.set(year, { sum: SERIES.map(() => 0), count: SERIES.map(() => 0) });
			}
			const bucket = buckets.get(year);
			SERIES.forEach((series, i) => {
				const value = parseFloat(row[series.key]);
				if (Number.isFinite(value)) {
					bucket.sum[i] += value;
					bucket.count[i] += 1;
				}
			});
		});

		return Array.from(buckets.keys()).sort((a, b) => a - b).map(year => {
			const bucket = buckets.get(year);
			return {
				year: year,
				values: bucket.sum.map((sum, i) => (bucket.count[i] ? sum / bucket.count[i] : 0))
			};
		});
	}

	/* ----------------------------------------------------------------------
	   小工具：建立 SVG 元素
	   ---------------------------------------------------------------------- */
	function createSvgNode(tag, attrs) {
		const node = document.createElementNS(SVG_NS, tag);
		Object.keys(attrs || {}).forEach(name => node.setAttribute(name, attrs[name]));
		return node;
	}

	/* 產生刻度值（0 起算，取好看的間距） */
	function makeTicks(maxValue) {
		const rawStep = maxValue / 6;
		const magnitude = Math.pow(10, Math.floor(Math.log10(rawStep)));
		const candidates = [1, 2, 2.5, 5, 10].map(n => n * magnitude);
		const step = candidates.find(n => n >= rawStep) || magnitude * 10;
		/* 最後一個刻度必須大於等於資料最大值，圖形才不會超出繪圖區 */
		const ticks = [];
		for (let value = 0; value < maxValue - step * 0.001; value += step) {
			ticks.push(Number(value.toFixed(6)));
		}
		ticks.push(Number((ticks.length * step).toFixed(6)));
		return ticks;
	}

	/* ----------------------------------------------------------------------
	   主要繪圖流程
	   ---------------------------------------------------------------------- */
	function render() {
		const data = aggregate(state.scope);
		svg.innerHTML = "";
		tooltip.hidden = true;

		if (data.length === 0) { return; }

		/* 只累計目前顯示中的資料層 */
		const activeIndexes = SERIES.map((_, i) => i).filter(i => state.visible[i]);
		const totals = data.map(point => activeIndexes.reduce((sum, i) => sum + point.values[i], 0));
		const maxTotal = Math.max(...totals, 1);
		const ticks = makeTicks(maxTotal);
		const yMax = ticks[ticks.length - 1];

		const minYear = data[0].year;
		const maxYear = data[data.length - 1].year;

		const xScale = year => PAD.left + (year - minYear) / (maxYear - minYear) * PLOT.width;
		const yScale = value => PAD.top + PLOT.height - value / yMax * PLOT.height;

		/* 1. 水平格線與 Y 軸刻度 */
		ticks.forEach(tick => {
			const y = yScale(tick);
			if (tick > 0) {
				svg.appendChild(createSvgNode("line", {
					class: "grid-line", x1: PAD.left, x2: PAD.left + PLOT.width, y1: y, y2: y
				}));
			}
			const label = createSvgNode("text", { class: "tick-text tick-text--y", x: PAD.left - 10, y: y });
			label.textContent = formatNumber(tick);
			svg.appendChild(label);
		});

		/* 2. 堆疊面積：由下往上累加 */
		const stackTop = data.map(() => 0);
		activeIndexes.forEach(index => {
			const lower = stackTop.slice();
			data.forEach((point, i) => { stackTop[i] += point.values[index]; });

			const upperPath = data.map((point, i) => `${xScale(point.year)},${yScale(stackTop[i])}`);
			const lowerPath = data.map((point, i) => `${xScale(point.year)},${yScale(lower[i])}`).reverse();

			svg.appendChild(createSvgNode("polygon", {
				class: "area",
				points: upperPath.concat(lowerPath).join(" "),
				fill: SERIES[index].color
			}));

			/* 在右側標註該層名稱（放在該層最後一年的中間高度，過長則自動換行） */
			const lastIndex = data.length - 1;
			const labelY = (yScale(stackTop[lastIndex]) + yScale(lower[lastIndex])) / 2;
			const lines = wrapWords(SERIES[index].name, 20);
			const label = createSvgNode("text", {
				class: "area-label",
				x: PAD.left + PLOT.width + 14,
				y: labelY - (lines.length - 1) * 9,
				fill: SERIES[index].color
			});
			lines.forEach((line, i) => {
				const tspan = createSvgNode("tspan", {
					x: PAD.left + PLOT.width + 14,
					dy: i === 0 ? 0 : 18
				});
				tspan.textContent = line;
				label.appendChild(tspan);
			});
			svg.appendChild(label);
		});

		/* 3. 座標軸 */
		svg.appendChild(createSvgNode("line", {
			class: "axis-line",
			x1: PAD.left, x2: PAD.left + PLOT.width,
			y1: PAD.top + PLOT.height, y2: PAD.top + PLOT.height
		}));
		svg.appendChild(createSvgNode("line", {
			class: "axis-line",
			x1: PAD.left, x2: PAD.left, y1: PAD.top, y2: PAD.top + PLOT.height
		}));

		/* 4. X 軸年份刻度：每 5 年一格，並補上頭尾 */
		const xTicks = [];
		for (let year = minYear; year <= maxYear; year += 5) { xTicks.push(year); }
		if (xTicks[xTicks.length - 1] !== maxYear) { xTicks.push(maxYear); }
		xTicks.forEach(year => {
			const label = createSvgNode("text", {
				class: "tick-text tick-text--x",
				x: xScale(year),
				y: PAD.top + PLOT.height + 24
			});
			label.textContent = year;
			svg.appendChild(label);
		});

		/* 5. Y 軸單位說明 */
		const axisTitle = createSvgNode("text", {
			class: "axis-title", x: PAD.left - 46, y: PAD.top - 18
		});
		axisTitle.textContent = "deaths per 100,000";
		svg.appendChild(axisTitle);

		/* 6. 滑鼠導引線與感應區 */
		const hoverGroup = createSvgNode("g", { visibility: "hidden" });
		const hoverLine = createSvgNode("line", {
			class: "hover-line", y1: PAD.top, y2: PAD.top + PLOT.height, x1: 0, x2: 0
		});
		hoverGroup.appendChild(hoverLine);
		const hoverDots = activeIndexes.map(index => {
			const dot = createSvgNode("circle", { class: "hover-dot", r: 4, fill: SERIES[index].color, cx: 0, cy: 0 });
			hoverGroup.appendChild(dot);
			return dot;
		});
		svg.appendChild(hoverGroup);

		const overlay = createSvgNode("rect", {
			x: PAD.left, y: PAD.top, width: PLOT.width, height: PLOT.height, fill: "transparent"
		});
		svg.appendChild(overlay);

		overlay.addEventListener("mousemove", event => {
			const box = svg.getBoundingClientRect();
			const ratio = VIEW.width / box.width;
			const svgX = (event.clientX - box.left) * ratio;
			const year = Math.round(minYear + (svgX - PAD.left) / PLOT.width * (maxYear - minYear));
			const pointIndex = Math.min(Math.max(year - minYear, 0), data.length - 1);
			const point = data[pointIndex];

			const x = xScale(point.year);
			hoverGroup.setAttribute("visibility", "visible");
			hoverLine.setAttribute("x1", x);
			hoverLine.setAttribute("x2", x);

			let accumulated = 0;
			activeIndexes.forEach((index, i) => {
				accumulated += point.values[index];
				hoverDots[i].setAttribute("cx", x);
				hoverDots[i].setAttribute("cy", yScale(accumulated));
			});

			showTooltip(point, activeIndexes, x / ratio, box);
		});

		overlay.addEventListener("mouseleave", () => {
			hoverGroup.setAttribute("visibility", "hidden");
			tooltip.hidden = true;
		});
	}

	/* 依字數把文字切成多行，供 SVG 標籤換行使用 */
	function wrapWords(text, maxLength) {
		const words = text.split(" ");
		const lines = [];
		let current = "";
		words.forEach(word => {
			if (current === "") { current = word; }
			else if ((current + " " + word).length <= maxLength) { current += " " + word; }
			else { lines.push(current); current = word; }
		});
		if (current !== "") { lines.push(current); }
		return lines;
	}

	function formatNumber(value) {
		return Number(value).toFixed(Math.abs(value) < 10 && value % 1 !== 0 ? 1 : 0);
	}

	function showTooltip(point, activeIndexes, pixelX, box) {
		const total = activeIndexes.reduce((sum, i) => sum + point.values[i], 0);
		const rows = activeIndexes.slice().reverse().map(i => `
			<div class="tooltip__row">
				<span class="tooltip__swatch" style="background:${SERIES[i].color}"></span>
				<span class="tooltip__name">${SERIES[i].name}</span>
				<span class="tooltip__value">${point.values[i].toFixed(2)}</span>
			</div>`).join("");

		tooltip.innerHTML = `<span class="tooltip__year">${point.year}</span>${rows}
			<div class="tooltip__total"><span>合計</span><span class="tooltip__value">${total.toFixed(2)}</span></div>`;
		tooltip.hidden = false;
		tooltip.style.left = `${pixelX}px`;
		tooltip.style.top = `${box.height * 0.55}px`;
	}

	/* ----------------------------------------------------------------------
	   圖例（可點擊切換顯示）
	   ---------------------------------------------------------------------- */
	function buildLegend() {
		legendBox.innerHTML = "";
		SERIES.slice().reverse().forEach(series => {
			const index = SERIES.indexOf(series);
			const item = document.createElement("li");
			item.className = "legend__item" + (state.visible[index] ? "" : " is-off");
			item.innerHTML = `<span class="legend__swatch" style="background:${series.color}"></span>${series.name}`;
			item.addEventListener("click", () => {
				/* 至少保留一層資料 */
				if (state.visible[index] && state.visible.filter(Boolean).length === 1) { return; }
				state.visible[index] = !state.visible[index];
				buildLegend();
				render();
			});
			legendBox.appendChild(item);
		});
	}

	/* ----------------------------------------------------------------------
	   下拉選單：所有國家平均 / 世界 / 各個國家
	   ---------------------------------------------------------------------- */
	function buildScopeSelect() {
		const countryCount = new Set(
			state.rows.filter(row => row.Code && row.Code !== "OWID_WRL").map(row => row.Entity)
		).size;

		const options = [
			{ value: "__ALL__", text: `所有國家平均（共 ${countryCount} 國）` }
		];
		state.entities.forEach(name => options.push({ value: name, text: name }));

		scopeSelect.innerHTML = "";
		options.forEach(option => {
			const node = document.createElement("option");
			node.value = option.value;
			node.textContent = option.text;
			scopeSelect.appendChild(node);
		});

		scopeSelect.value = state.scope;
		scopeSelect.addEventListener("change", () => {
			state.scope = scopeSelect.value;
			updateTitle();
			render();
		});
	}

	function updateTitle() {
		const label = state.scope === "__ALL__" ? "All countries (average)" : state.scope;
		titleText.textContent = `Death rates from air pollution, ${label}`;
		scopeHint.textContent = state.scope === "__ALL__"
			? "＝ 各年度所有國家數值的平均"
			: "＝ 單一項目的原始數值";
	}

	/* ----------------------------------------------------------------------
	   啟動
	   ---------------------------------------------------------------------- */
	fetch(CSV_URL)
		.then(response => response.text())
		.then(text => {
			state.rows = parseCsv(text);
			state.entities = Array.from(new Set(state.rows.map(row => row.Entity))).sort();
			buildScopeSelect();
			buildLegend();
			updateTitle();
			render();
			window.addEventListener("resize", render);
		})
		.catch(error => {
			document.getElementById("chart-title").textContent = "資料讀取失敗：" + error.message;
		});
})();
