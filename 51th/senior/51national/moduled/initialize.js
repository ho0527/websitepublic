/* ==========================================================================
   第51屆全國技能競賽 網頁技術 模組D - 房屋交易平台
   共用程式：API 呼叫、登入狀態、導覽列、搜尋條件、分頁與房屋卡片
   ========================================================================== */

/** API 進入點（PATH_INFO 形式，不經過轉址） */
const API_BASE = "api/index.php";

/** 每頁筆數，與後端 config.php 的 per_page 相同 */
const PER_PAGE = 10;

/** 錯誤訊息代碼對應的中文說明 */
const ERROR_MESSAGE = {
	MSG_INVALID_LOGIN: "登入失敗，帳號或密碼有誤",
	MSG_USER_EXISTS: "此 Email 已被註冊",
	MSG_INVALID_TOKEN: "登入狀態已失效，請重新登入",
	MSG_PERMISSION_DENY: "權限不足",
	MSG_MISSING_FIELD: "缺少必要欄位",
	MSG_WRONG_DATA_TYPE: "資料格式錯誤",
	MSG_IMAGE_CAN_NOT_PROCESS: "圖片格式錯誤，請上傳圖片檔",
	MSG_INVALID_COVER_INDEX: "封面索引錯誤",
	MSG_HOUSE_NOT_EXISTS: "不存在的房屋",
	MSG_HOUSE_APPLIED: "此房屋的精選申請正在審核中",
	MSG_HOUSE_ADVERTISED: "此房屋已是精選房屋",
	MSG_APPLICATION_NOT_EXISTS: "不存在的申請",
	MSG_ALREADY_ADVERTISED: "此申請已審核完成",
	MSG_AD_NOT_EXISTS: "不存在的精選房屋",
	MSG_NOT_FOUND: "找不到對應的資源",
	MSG_SERVER_ERROR: "伺服器發生錯誤",
};

/* --------------------------------------------------------------------------
   登入狀態（存放於 localStorage）
   -------------------------------------------------------------------------- */
const Session = {
	KEY: "51nationalmoduled-user",

	/** 取得目前登入的使用者，未登入時回傳 null */
	user() {
		try {
			return JSON.parse(localStorage.getItem(this.KEY)) || null;
		} catch (error) {
			return null;
		}
	},

	/** 儲存登入資訊 */
	save(user) {
		localStorage.setItem(this.KEY, JSON.stringify(user));
	},

	/** 清除登入資訊 */
	clear() {
		localStorage.removeItem(this.KEY);
	},

	/** 取得 API 驗證用的 Token */
	token() {
		const user = this.user();
		return user ? user.token : "";
	},

	/** 是否為管理員 */
	isAdmin() {
		const user = this.user();
		return !!user && user.role === "ADMIN";
	},
};

/* --------------------------------------------------------------------------
   API 呼叫
   -------------------------------------------------------------------------- */

/** API 回傳失敗時拋出的錯誤 */
class ApiError extends Error {
	constructor(code, status) {
		super(ERROR_MESSAGE[code] || code);
		this.code = code;
		this.status = status;
	}
}

/**
 * 呼叫 API
 *
 * @param {string} method  HTTP 方法
 * @param {string} path    路徑，例如 /house
 * @param {object} options { query, json, form, auth }
 * @returns {Promise<any>} 回應中的 data
 */
async function api(method, path, options = {}) {
	const url = new URL(API_BASE + path, location.href);

	if (options.query) {
		Object.entries(options.query).forEach(([key, value]) => {
			if (value !== undefined && value !== null && value !== "") {
				url.searchParams.set(key, value);
			}
		});
	}

	const headers = { Accept: "application/json" };
	let body;

	if (options.json !== undefined) {
		headers["Content-Type"] = "application/json";
		body = JSON.stringify(options.json);
	} else if (options.form !== undefined) {
		body = options.form;
	}

	if (options.auth !== false) {
		const token = Session.token();
		if (token) {
			headers["X-User-Token"] = token;
		}
	}

	const response = await fetch(url.toString(), { method, headers, body });
	const result = await response.json().catch(() => ({ success: false, message: "MSG_SERVER_ERROR" }));

	if (!result.success) {
		// Token 失效時清除登入狀態，避免畫面持續顯示已登入
		if (result.message === "MSG_INVALID_TOKEN") {
			Session.clear();
		}
		throw new ApiError(result.message, response.status);
	}

	return result.data;
}

/* --------------------------------------------------------------------------
   共用工具
   -------------------------------------------------------------------------- */

/** HTML 跳脫，避免 XSS */
function escapeHtml(value) {
	return String(value === null || value === undefined ? "" : value)
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#39;");
}

/** 千分位金額格式 */
function formatMoney(value) {
	return "$" + Number(value || 0).toLocaleString("en-US");
}

/** 單價 = 價格 / 坪數 */
function unitPrice(price, square) {
	if (!square) {
		return 0;
	}
	return Math.round(Number(price) / Number(square));
}

/** 取得網址上的查詢參數 */
function queryParam(key) {
	return new URLSearchParams(location.search).get(key);
}

/** 顯示訊息區塊 */
function showMessage(element, text, type = "error") {
	if (!element) {
		return;
	}
	element.textContent = text;
	element.className = "message show " + type;
}

/** 隱藏訊息區塊 */
function hideMessage(element) {
	if (element) {
		element.className = "message";
	}
}

/* --------------------------------------------------------------------------
   導覽列
   -------------------------------------------------------------------------- */

/**
 * 依照身分權限繪製導覽列
 *
 * @param {string} active 目前頁面代號，用來標示選取狀態
 */
function renderHeader(active) {
	const header = document.getElementById("site-header");
	if (!header) {
		return;
	}

	const user = Session.user();
	const links = [{ id: "index", href: "index.html", text: "首頁" }];

	if (user) {
		links.push({ id: "publish", href: "publish.html", text: "刊登列表" });
		if (user.role === "ADMIN") {
			links.push({ id: "application", href: "application.html", text: "申請列表" });
			links.push({ id: "ads", href: "ads.html", text: "精選房屋列表" });
		}
	} else {
		links.push({ id: "signin", href: "signin.html", text: "登入" });
		links.push({ id: "signup", href: "signup.html", text: "註冊" });
	}

	const navHtml = links
		.map((link) => `<a id="nav-${link.id}" href="${link.href}"${link.id === active ? ' class="active"' : ""}>${escapeHtml(link.text)}</a>`)
		.join("");

	const userHtml = user
		? `<span class="badge-status">${escapeHtml(user.nickname)}</span><button type="button" id="nav-signout">登出</button>`
		: "";

	header.innerHTML = `
		<div class="inner">
			<a class="brand" href="index.html">
				<span class="mark">HT</span>
				<span>Best platform to deal the house</span>
			</a>
			<nav class="nav">${navHtml}${userHtml}</nav>
		</div>
	`;

	const signOutButton = document.getElementById("nav-signout");
	if (signOutButton) {
		signOutButton.addEventListener("click", async () => {
			try {
				await api("POST", "/user/logout");
			} catch (error) {
				// 即使伺服器端已失效，本機仍要清除登入狀態
			}
			Session.clear();
			location.href = "index.html";
		});
	}
}

/** 需要登入才能瀏覽的頁面 */
function requireLogin() {
	if (!Session.user()) {
		location.href = "signin.html";
		return false;
	}
	return true;
}

/** 需要管理員權限才能瀏覽的頁面 */
function requireAdmin() {
	if (!Session.isAdmin()) {
		location.href = "index.html";
		return false;
	}
	return true;
}

/* --------------------------------------------------------------------------
   搜尋條件區塊
   -------------------------------------------------------------------------- */

/**
 * 讀取搜尋 / 排序條件，組成 API 查詢參數
 *
 * @param {object} options { sort: 是否含排序欄位, status: 是否含審核狀態 }
 */
function readSearchConditions(options = {}) {
	const query = {};

	const keyword = document.getElementById("keyword");
	if (keyword && keyword.value.trim() !== "") {
		query.title = keyword.value.trim();
	}

	const minPrice = document.getElementById("min-price");
	const maxPrice = document.getElementById("max-price");
	if (minPrice && minPrice.value !== "") {
		query.min_price = minPrice.value;
	}
	if (maxPrice && maxPrice.value !== "") {
		query.max_price = maxPrice.value;
	}

	// 房數：1 / 2 / 3 為完全相符，More 為 4 房以上
	const room = document.querySelector('input[name="room"]:checked');
	if (room && room.value !== "") {
		if (room.value === "more") {
			query.min_room = 4;
		} else {
			query.room = room.value;
		}
	}

	// 屋齡區間
	const age = document.querySelector('input[name="age"]:checked');
	if (age && age.value !== "") {
		const [minAge, maxAge] = age.value.split("-");
		if (minAge !== "") {
			query.min_age = minAge;
		}
		if (maxAge !== undefined && maxAge !== "") {
			query.max_age = maxAge;
		}
	}

	if (options.sort !== false) {
		const sortBy = document.querySelector('input[name="sort_by"]:checked');
		query.sort_by = sortBy ? sortBy.value : "published_at";
	}

	if (options.status) {
		const status = document.querySelector('input[name="status"]:checked');
		query.status = status ? status.value : "applied";
	}

	return query;
}

/** 綁定搜尋區塊的事件（送出按鈕、Enter、條件變更） */
function bindSearchEvents(onSearch) {
	const searchButton = document.getElementById("search-button");
	if (searchButton) {
		searchButton.addEventListener("click", onSearch);
	}

	const keyword = document.getElementById("keyword");
	if (keyword) {
		keyword.addEventListener("keydown", (event) => {
			if (event.key === "Enter") {
				onSearch();
			}
		});
	}

	document.querySelectorAll('.search-panel input[type="radio"]').forEach((radio) => {
		radio.addEventListener("change", onSearch);
	});
}

/**
 * 綁定升 / 降冪切換器
 *
 * @param {function} onChange 切換後的回呼
 * @returns {function(): string} 取得目前排序方向的函式
 */
function bindOrderToggle(onChange) {
	const button = document.getElementById("order-toggle");
	let order = "desc";

	const paint = () => {
		if (button) {
			button.textContent = order === "desc" ? "降冪 ↓" : "升冪 ↑";
			button.dataset.order = order;
		}
	};

	if (button) {
		button.addEventListener("click", () => {
			order = order === "desc" ? "asc" : "desc";
			paint();
			onChange();
		});
	}
	paint();

	return () => order;
}

/* --------------------------------------------------------------------------
   分頁切換器
   -------------------------------------------------------------------------- */

/**
 * 繪製分頁切換器
 *
 * @param {HTMLElement} container  容器
 * @param {number}      page       目前頁碼（由 1 開始）
 * @param {number}      totalCount 總筆數
 * @param {function}    onChange   換頁回呼
 */
function renderPagination(container, page, totalCount, onChange) {
	if (!container) {
		return;
	}

	const totalPage = Math.max(1, Math.ceil(totalCount / PER_PAGE));
	container.innerHTML = "";

	const addButton = (text, targetPage, disabled, current) => {
		const button = document.createElement("button");
		button.type = "button";
		button.textContent = text;
		button.disabled = !!disabled;
		if (current) {
			button.classList.add("current");
		}
		button.addEventListener("click", () => onChange(targetPage));
		container.appendChild(button);
	};

	addButton("◀", page - 1, page <= 1, false);

	// 目前頁碼前後各顯示兩頁
	const start = Math.max(1, page - 2);
	const end = Math.min(totalPage, start + 4);
	for (let index = start; index <= end; index++) {
		addButton(String(index), index, false, index === page);
	}

	addButton("▶", page + 1, page >= totalPage, false);
}

/* --------------------------------------------------------------------------
   房屋卡片
   -------------------------------------------------------------------------- */

/**
 * 產生房屋卡片的 HTML
 *
 * @param {object} house   房屋資料
 * @param {object} options { link: 是否可點擊進入詳細頁, extra: 額外的 HTML, actions: 按鈕 HTML }
 */
function houseCardHtml(house, options = {}) {
	const cover = house.cover_image_url
		? `<img class="thumb" src="${escapeHtml(house.cover_image_url)}" alt="${escapeHtml(house.title)}">`
		: `<div class="thumb"></div>`;

	const titleHtml = options.link === false
		? `<div class="title">${escapeHtml(house.title)}</div>`
		: `<a class="title" href="house.html?id=${encodeURIComponent(house.id)}">${escapeHtml(house.title)}</a>`;

	return `
		<article class="house-card" data-id="${escapeHtml(house.id)}">
			${house.is_ad ? '<span class="badge-ad">Advertisement</span>' : ""}
			${cover}
			<div class="body">
				${titleHtml}
				<div class="price">Price ${escapeHtml(formatMoney(house.price))}</div>
				<div class="meta">${escapeHtml(house.square)} square(s) | ${escapeHtml(formatMoney(unitPrice(house.price, house.square)))} per square | ${escapeHtml(house.room)} rooms</div>
				${options.extra || ""}
				<div class="actions">${options.actions || ""}</div>
			</div>
		</article>
	`;
}
