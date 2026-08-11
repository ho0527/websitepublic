/* ==========================================================================
   Module H - WorldSkills 版型互動腳本
   完全不使用任何 JavaScript 函式庫（題目要求），只用原生 DOM API
   內容：平滑捲動導覽 / 輪播 / Continue reading / 背景視差 /
         手勢標誌動線 / 結果清單搜尋（即時、AND、大小寫、自動完成、CTRL+S）
   ========================================================================== */
(function () {
	"use strict";

	var STORAGE_KEY = "wsc2013_module_h_search";   /* CTRL+S 儲存搜尋字串用的鍵值 */
	var SLIDE_INTERVAL = 5000;                      /* 每張投影片顯示 5 秒 */

	/* ======================================================================
	   1. 平滑捲動（自製 easing，取代 jQuery 的 animate）
	   ====================================================================== */
	var scrollTimer = null;

	function smoothScrollTo(targetY, duration) {
		var startY = window.pageYOffset;
		var distance = targetY - startY;
		var startTime = null;
		duration = duration || 700;

		if (scrollTimer) { cancelAnimationFrame(scrollTimer); }

		function step(now) {
			if (startTime === null) { startTime = now; }
			var p = Math.min((now - startTime) / duration, 1);
			/* easeInOutCubic：起訖平緩、中段快速 */
			var eased = p < 0.5 ? 4 * p * p * p : 1 - Math.pow(-2 * p + 2, 3) / 2;
			window.scrollTo(0, startY + distance * eased);
			if (p < 1) { scrollTimer = requestAnimationFrame(step); }
		}
		scrollTimer = requestAnimationFrame(step);
	}

	function scrollToSection(id) {
		var el = document.getElementById(id);
		if (!el) { return; }
		var navH = document.querySelector(".nav").offsetHeight;
		smoothScrollTo(el.getBoundingClientRect().top + window.pageYOffset - navH + 2, 800);
	}

	/* 導覽按鈕：點擊平滑捲動到對應區塊 */
	var navButtons = document.querySelectorAll(".navbtn");
	Array.prototype.forEach.call(navButtons, function (btn) {
		btn.addEventListener("click", function () {
			scrollToSection(btn.getAttribute("data-target"));
		});
	});

	/* ======================================================================
	   2. 輪播：3 張圖淡入 + 位移，每 5 秒自動換下一張並循環
	   ====================================================================== */
	var slides = document.querySelectorAll(".slide");
	var dots = document.querySelectorAll(".dot");
	var slideIndex = 0;
	var slideTimer = null;

	function showSlide(index) {
		slideIndex = (index + slides.length) % slides.length;
		Array.prototype.forEach.call(slides, function (img, i) {
			img.classList.toggle("is-active", i === slideIndex);
		});
		Array.prototype.forEach.call(dots, function (dot, i) {
			dot.classList.toggle("is-active", i === slideIndex);
		});
	}

	/* 每次手動切換都重新計時，避免剛按完馬上又自動跳頁 */
	function restartSlideTimer() {
		if (slideTimer) { clearInterval(slideTimer); }
		slideTimer = setInterval(function () { showSlide(slideIndex + 1); }, SLIDE_INTERVAL);
	}

	document.getElementById("prev").addEventListener("click", function () {
		showSlide(slideIndex - 1);
		restartSlideTimer();
	});
	document.getElementById("next").addEventListener("click", function () {
		showSlide(slideIndex + 1);
		restartSlideTimer();
	});
	Array.prototype.forEach.call(dots, function (dot) {
		dot.addEventListener("click", function () {
			showSlide(parseInt(dot.getAttribute("data-index"), 10));
			restartSlideTimer();
		});
	});
	restartSlideTimer();

	/* ======================================================================
	   3. Continue reading：以 max-height 做 slide-down / slide-up
	   ====================================================================== */
	Array.prototype.forEach.call(document.querySelectorAll(".showall"), function (btn) {
		btn.addEventListener("click", function () {
			var box = document.getElementById(btn.getAttribute("data-target"));
			if (!box) { return; }
			var opened = box.classList.toggle("is-open");
			btn.textContent = opened ? "Show less" : "Continue reading...";
		});
	});

	/* ======================================================================
	   4. 區塊背景視差：捲動時改變背景圖的垂直位置
	   ====================================================================== */
	var parallaxSections = ["about", "competition41", "competition40"].map(function (id) {
		return document.getElementById(id);
	});

	function updateParallax() {
		parallaxSections.forEach(function (sec) {
			if (!sec) { return; }
			var rect = sec.getBoundingClientRect();
			if (rect.bottom < -200 || rect.top > window.innerHeight + 200) { return; }
			/* 區塊在視窗中的相對位置 -1 ~ 1，乘上係數得到位移量 */
			var ratio = (rect.top + rect.height / 2 - window.innerHeight / 2) / window.innerHeight;
			sec.style.setProperty("--shift", (ratio * 140).toFixed(1) + "px");
		});
	}

	/* ======================================================================
	   5. 手勢標誌：依目前所在區塊移動到 A / B / C，搜尋框取得焦點時移到標題左側
	   ====================================================================== */
	var hand = document.getElementById("handsymbol");
	var handLocked = false;      /* 搜尋框取得焦點時鎖定，捲動也不再移動 */
	var currentSpot = "";

	/* 各定位點以視窗尺寸計算，確保任何解析度下都留在畫面內 */
	function spotPosition(spot) {
		var w = window.innerWidth, h = window.innerHeight;
		var half = w / 2;
		switch (spot) {
			case "a": return { left: half + 300, top: h * 0.22 };   /* About 區右側 */
			case "b": return { left: half - 400, top: h * 0.45 };   /* 41st 區左側 */
			case "c": return { left: half + 300, top: h * 0.62 };   /* 40th 區右側 */
			case "search": return { left: half - 489, top: 118 };   /* Results 標題左側 */
			default: return { left: 12, top: 14 };                  /* 起點：導覽列左上 */
		}
	}

	function moveHand(spot) {
		if (spot === currentSpot) { return; }
		currentSpot = spot;
		var pos = spotPosition(spot);
		hand.style.left = Math.max(8, Math.min(window.innerWidth - 60, pos.left)) + "px";
		hand.style.top = Math.max(8, Math.min(window.innerHeight - 70, pos.top)) + "px";
		hand.className = spot ? "at-" + spot : "";
	}

	/* 依捲動位置判斷目前主要顯示的區塊 */
	function currentSection() {
		var mid = window.innerHeight * 0.45;
		var map = [["about", "a"], ["competition41", "b"], ["competition40", "c"], ["result", ""]];
		var found = "";
		map.forEach(function (pair) {
			var el = document.getElementById(pair[0]);
			if (!el) { return; }
			var rect = el.getBoundingClientRect();
			if (rect.top <= mid && rect.bottom > mid) { found = pair[1]; }
		});
		return found;
	}

	function markCurrentNav() {
		var mid = window.innerHeight * 0.45;
		Array.prototype.forEach.call(navButtons, function (btn) {
			var el = document.getElementById(btn.getAttribute("data-target"));
			var rect = el.getBoundingClientRect();
			btn.classList.toggle("is-current", rect.top <= mid && rect.bottom > mid);
		});
	}

	function onScroll() {
		updateParallax();
		markCurrentNav();
		if (!handLocked) { moveHand(currentSection()); }
	}

	window.addEventListener("scroll", onScroll, { passive: true });
	window.addEventListener("resize", function () {
		currentSpot = "__";              /* 強制重新定位 */
		onScroll();
		if (handLocked) { moveHand("search"); }
	});

	/* 點擊手勢標誌 → 平滑回到頁首 */
	hand.addEventListener("click", function () { smoothScrollTo(0, 800); });

	/* ======================================================================
	   6. 結果清單：解析原始資料
	   ====================================================================== */
	var records = [];

	(function parseRecords() {
		var data = document.getElementById("resultdata");
		if (!data) { return; }
		Array.prototype.forEach.call(data.children, function (block) {
			var h2 = block.querySelector("h2");
			if (!h2) { return; }
			var head = h2.textContent.split("-");
			var year = head[0].trim();
			var host = (head[1] || "").trim();

			Array.prototype.forEach.call(block.querySelectorAll(":scope > ul > li"), function (tradeLi) {
				var titleDiv = tradeLi.querySelector("div");
				if (!titleDiv) { return; }
				var parts = titleDiv.textContent.split("-");
				var tradeNo = parts.shift().trim();
				var tradeName = parts.join("-").trim();

				Array.prototype.forEach.call(tradeLi.querySelectorAll("ul > li"), function (li) {
					/* 原始資料中有空白的分隔項目，沒有國家屬性者直接略過 */
					if (!li.getAttribute("data-country")) { return; }
					records.push({
						year: year,
						host: host,
						tradeNo: tradeNo,
						trade: tradeName,
						medal: (li.getAttribute("title") || "").trim(),
						name: li.textContent.trim(),
						country: (li.getAttribute("data-country") || "").trim()
					});
				});
			});
		});
	}());

	/* 自動完成的候選字庫：國家、技能、選手姓名、獎牌 */
	var suggestions = (function () {
		var set = {};
		records.forEach(function (r) {
			set[r.country] = 1;
			set[r.trade] = 1;
			set[r.medal] = 1;
			set[r.name] = 1;
			set[r.year] = 1;
		});
		return Object.keys(set).filter(function (t) { return t.length > 0; }).sort();
	}());

	var view = document.getElementById("resultview");
	var searchBox = document.getElementById("searchbox");
	var caseBox = document.getElementById("casesensitive");
	var acList = document.getElementById("autocomplete");
	var countLabel = document.getElementById("resultcount");

	function escapeHtml(text) {
		return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
	}

	/* 把符合的關鍵字用 <mark> 標起來 */
	function highlight(text, terms) {
		var html = escapeHtml(text);
		terms.forEach(function (term) {
			if (!term) { return; }
			var safe = term.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
			html = html.replace(new RegExp("(" + safe + ")", caseBox.checked ? "g" : "gi"), "<mark>$1</mark>");
		});
		return html;
	}

	/* 依「+」拆解搜尋字串，每個詞都必須符合（AND 條件） */
	function parseTerms(query) {
		return query.split("+").map(function (t) { return t.trim(); }).filter(function (t) { return t.length > 0; });
	}

	function matches(record, terms) {
		var haystack = [record.medal, record.name, record.country, record.tradeNo, record.trade, record.year, record.host].join(" | ");
		if (!caseBox.checked) { haystack = haystack.toLowerCase(); }
		return terms.every(function (term) {
			return haystack.indexOf(caseBox.checked ? term : term.toLowerCase()) >= 0;
		});
	}

	/* 產生單筆資料的列（6 個欄位皆可點擊作為搜尋關鍵字） */
	function rowHtml(r, terms) {
		var medalClass = r.medal.toLowerCase();
		return '<div class="resultrow ' + medalClass + '">' +
			'<span class="medal kw" data-kw="' + escapeHtml(r.medal) + '">' + escapeHtml(r.medal) + "</span>" +
			'<span class="who kw" data-kw="' + escapeHtml(r.name) + '">' + highlight(r.name, terms) + "</span>" +
			'<span class="country kw" data-kw="' + escapeHtml(r.country) + '">' + highlight(r.country, terms) + "</span>" +
			'<span class="trade kw" data-kw="' + escapeHtml(r.trade) + '">' +
				escapeHtml(r.tradeNo) + " - " + highlight(r.trade, terms) + "</span>" +
			'<span class="event kw" data-kw="' + escapeHtml(r.year) + '">' +
				escapeHtml(r.year) + " &middot; " + escapeHtml(r.host) + "</span>" +
			"</div>";
	}

	/* 依搜尋字串重新輸出清單，依競賽年份分組 */
	function render() {
		var query = searchBox.value;
		var terms = parseTerms(query);
		var hits = records.filter(function (r) { return matches(r, terms); });

		countLabel.textContent = hits.length + " / " + records.length;

		if (hits.length === 0) {
			view.innerHTML = '<div class="noresult">There is no result for keyword &ldquo;' +
				escapeHtml(query) + "&rdquo;</div>";
			return;
		}

		var html = "";
		var lastGroup = "";
		hits.forEach(function (r) {
			var group = r.year + " - " + r.host;
			if (group !== lastGroup) {
				html += '<div class="groupheader">' + escapeHtml(group) + " WorldSkills Competition</div>";
				lastGroup = group;
			}
			html += rowHtml(r, terms);
		});
		view.innerHTML = html;
	}

	/* ======================================================================
	   7. 搜尋互動：即時篩選、自動完成、點擊關鍵字、CTRL+S 記憶
	   ====================================================================== */

	/* 取得游標所在（最後一個）關鍵字，供自動完成使用 */
	function lastTerm() {
		var parts = searchBox.value.split("+");
		return parts[parts.length - 1].trim();
	}

	function replaceLastTerm(word) {
		var parts = searchBox.value.split("+");
		parts[parts.length - 1] = word;
		searchBox.value = parts.join("+");
	}

	function closeAutocomplete() {
		acList.classList.remove("is-open");
		acList.innerHTML = "";
	}

	/* 自動完成清單最多顯示 5 筆，並跟隨 Case Sensitive 設定 */
	function updateAutocomplete() {
		var term = lastTerm();
		if (term.length === 0) { closeAutocomplete(); return; }

		var needle = caseBox.checked ? term : term.toLowerCase();
		var starts = [], contains = [];
		suggestions.forEach(function (word) {
			var target = caseBox.checked ? word : word.toLowerCase();
			if (target === needle) { return; }
			if (target.indexOf(needle) === 0) { starts.push(word); }
			else if (target.indexOf(needle) > 0) { contains.push(word); }
		});
		/* 開頭相符的優先，其次才是包含關鍵字者，最多 5 筆 */
		var hits = starts.concat(contains).slice(0, 5);

		if (hits.length === 0) { closeAutocomplete(); return; }

		acList.innerHTML = hits.map(function (w) {
			return "<li>" + escapeHtml(w) + "</li>";
		}).join("");
		acList.classList.add("is-open");
	}

	acList.addEventListener("mousedown", function (e) {
		/* 用 mousedown 才不會先觸發 input 的 blur 而關掉清單 */
		if (e.target.tagName === "LI") {
			e.preventDefault();
			replaceLastTerm(e.target.textContent);
			closeAutocomplete();
			render();
			searchBox.focus();
		}
	});

	searchBox.addEventListener("input", function () {
		updateAutocomplete();
		render();
	});

	caseBox.addEventListener("change", function () {
		updateAutocomplete();
		render();
	});

	/* CTRL + S：記住目前搜尋字串，下次開啟瀏覽器自動還原 */
	searchBox.addEventListener("keydown", function (e) {
		if ((e.ctrlKey || e.metaKey) && (e.key === "s" || e.key === "S")) {
			e.preventDefault();
			try {
				localStorage.setItem(STORAGE_KEY, searchBox.value);
				flashMessage("Search phrase saved: “" + searchBox.value + "”");
			} catch (err) { /* 隱私模式下無法寫入時忽略 */ }
		}
		if (e.key === "Escape") { closeAutocomplete(); }
	});

	/* 搜尋框取得焦點：手勢標誌移到 Results 標題左邊並固定 */
	searchBox.addEventListener("focus", function () {
		handLocked = true;
		moveHand("search");
	});

	searchBox.addEventListener("blur", function () {
		handLocked = false;
		currentSpot = "__";
		moveHand(currentSection());
		setTimeout(closeAutocomplete, 120);
	});

	/* 點擊清單中的關鍵字即搜尋；按住 SHIFT 點擊則以「+」追加（AND） */
	view.addEventListener("mousedown", function (e) {
		if (e.shiftKey) { e.preventDefault(); }        /* 避免 SHIFT 點擊造成反白 */
	});

	view.addEventListener("click", function (e) {
		var target = e.target.closest ? e.target.closest(".kw") : null;
		if (!target) { return; }
		var keyword = target.getAttribute("data-kw");
		if (e.shiftKey) {
			searchBox.value = searchBox.value.trim().length ? searchBox.value.trim() + "+" + keyword : keyword;
		} else {
			searchBox.value = keyword;
		}
		closeAutocomplete();
		render();
	});

	/* 右下角短暫提示訊息 */
	function flashMessage(text) {
		var box = document.createElement("div");
		box.textContent = text;
		box.style.cssText = "position:fixed;right:20px;bottom:20px;z-index:999;padding:10px 16px;" +
			"background:#182744;color:#fff;border:1px solid #f0b429;border-radius:8px;font-size:13px;" +
			"box-shadow:0 8px 20px rgba(0,0,0,.4)";
		document.body.appendChild(box);
		setTimeout(function () { box.parentNode && box.parentNode.removeChild(box); }, 2200);
	}

	/* ======================================================================
	   8. 初始化
	   ====================================================================== */
	try {
		var saved = localStorage.getItem(STORAGE_KEY);
		if (saved) { searchBox.value = saved; }
	} catch (err) { /* 忽略 */ }

	render();
	showSlide(0);
	onScroll();
}());
