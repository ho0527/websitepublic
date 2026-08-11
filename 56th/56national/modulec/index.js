/**
 * 模組 C - 南港展覽館服務行動網頁（前端）
 * 資料全部來自 api/ 目錄下的 PHP 後端，前端只負責呈現與互動。
 */

/* ------------------------------------------------------------------ */
/* 常數與狀態                                                          */
/* ------------------------------------------------------------------ */

const API_BASE = "api/";
const DEFAULT_LOCATION = { latitude: 25.0561, longitude: 121.6178 };
const EVENT_PAGE_SIZE = 8;
const DETAIL_REFRESH_MS = 10000;

const STORAGE_KEYS = {
    theme: "modulec-theme",
    sort: "modulec-sort",
    pinned: "modulec-pinned"
};

/** 天氣圖示的路徑資料（取自題目素材 svn icons/*.svg，viewBox 0 0 96 96） */
const WEATHER_ICON_PATHS = {
    sunny: [
        "M62.142,35.858c0.512,0,1.024-0.195,1.414-0.586l2.829-2.829c0.781-0.781,0.781-2.047,0-2.828c-0.781-0.781-2.048-0.781-2.828,0l-2.829,2.829c-0.781,0.781-0.781,2.047,0,2.828C61.118,35.663,61.63,35.858,62.142,35.858z",
        "M30,48c0-1.104-0.896-2-2-2h-4c-1.104,0-2,0.896-2,2s0.896,2,2,2h4C29.104,50,30,49.104,30,48z",
        "M32.444,60.728l-2.829,2.829c-0.781,0.781-0.781,2.047,0,2.828c0.39,0.391,0.902,0.586,1.414,0.586c0.512,0,1.024-0.195,1.414-0.586l2.829-2.829c0.781-0.781,0.781-2.047,0-2.828C34.492,59.947,33.224,59.947,32.444,60.728z",
        "M32.444,35.272c0.39,0.391,0.902,0.586,1.414,0.586s1.024-0.195,1.414-0.586c0.781-0.781,0.781-2.047,0-2.828l-2.829-2.829c-0.78-0.781-2.048-0.781-2.828,0c-0.781,0.781-0.781,2.047,0,2.828L32.444,35.272z",
        "M48,30c1.104,0,2-0.896,2-2v-4c0-1.104-0.896-2-2-2s-2,0.896-2,2v4C46,29.104,46.896,30,48,30z",
        "M72,46h-4c-1.104,0-2,0.896-2,2s0.896,2,2,2h4c1.104,0,2-0.896,2-2S73.104,46,72,46z",
        "M63.556,60.728c-0.78-0.781-2.048-0.781-2.828,0c-0.781,0.781-0.781,2.047,0,2.828l2.829,2.829c0.39,0.391,0.902,0.586,1.414,0.586c0.512,0,1.023-0.195,1.414-0.586c0.781-0.781,0.781-2.047,0-2.828L63.556,60.728z",
        "M48,66c-1.104,0-2,0.896-2,2v4c0,1.104,0.896,2,2,2s2-0.896,2-2v-4C50,66.896,49.104,66,48,66z",
        "M48,34c-7.72,0-14,6.28-14,14s6.28,14,14,14s14-6.28,14-14S55.72,34,48,34z M48,58c-5.514,0-10-4.486-10-10s4.486-10,10-10s10,4.486,10,10S53.514,58,48,58z"
    ],
    cloudy: [
        "M66,40c-0.507,0-1.112,0.079-1.688,0.184C62.218,33.012,55.663,28,48,28c-7.664,0-14.218,5.012-16.312,12.184C31.112,40.079,30.507,40,30,40c-6.065,0-11,4.935-11,11s4.935,11,11,11h36c6.065,0,11-4.935,11-11S72.065,40,66,40z M66,58H30c-3.86,0-7-3.141-7-7s3.14-7,7-7c0.277,0,0.723,0.068,1.194,0.162V46c0,1.104,0.896,2,2,2s2-0.896,2-2v-3.226C36.27,36.524,41.632,32,48,32c6.371,0,11.735,4.529,12.808,10.784V46c0,1.104,0.896,2,2,2c1.105,0,2-0.896,2-2v-1.837C65.278,44.069,65.726,44,66,44c3.859,0,7,3.141,7,7S69.859,58,66,58z"
    ],
    rainy: [
        "M66,40c-0.507,0-1.112,0.079-1.688,0.184C62.217,33.012,55.663,28,48,28s-14.218,5.012-16.311,12.184C31.112,40.079,30.507,40,30,40c-6.065,0-11,4.935-11,11s4.935,11,11,11c1.104,0,2-0.896,2-2s-0.896-2-2-2c-3.86,0-7-3.141-7-7s3.14-7,7-7c0.277,0,0.723,0.068,1.193,0.162V46c0,1.104,0.896,2,2,2s2-0.896,2-2v-3.221C36.267,36.527,41.63,32,48,32s11.732,4.527,12.807,10.779V46c0,1.104,0.896,2,2,2s2-0.896,2-2v-1.838C65.277,44.068,65.722,44,66,44c3.859,0,7,3.141,7,7s-3.141,7-7,7c-1.104,0-2,0.896-2,2s0.896,2,2,2c6.065,0,11-4.935,11-11S72.065,40,66,40z",
        "M49.485,52.06c-1.073-0.27-2.158,0.384-2.426,1.455l-6,24c-0.268,1.072,0.384,2.157,1.455,2.426C42.677,79.981,42.84,80,43.001,80c0.896,0,1.711-0.606,1.939-1.515l6-24C51.208,53.413,50.557,52.328,49.485,52.06z",
        "M57.484,58.06c-1.072-0.271-2.157,0.384-2.425,1.455l-3,12c-0.268,1.072,0.384,2.158,1.456,2.426c0.163,0.041,0.326,0.06,0.486,0.06c0.896,0,1.712-0.606,1.939-1.515l2.999-12C59.208,59.413,58.556,58.327,57.484,58.06z",
        "M38.484,58.06c-1.069-0.271-2.157,0.384-2.425,1.455l-3,12c-0.268,1.072,0.384,2.158,1.456,2.426c0.163,0.041,0.326,0.06,0.486,0.06c0.896,0,1.712-0.606,1.939-1.515l3-12C40.208,59.413,39.556,58.327,38.484,58.06z"
    ]
};

const state = {
    view: "parking",
    previousView: "parking",
    parkingLots: [],
    selectedParkingId: null,
    detailTimer: null,
    sortMode: readStorage(STORAGE_KEYS.sort, "alphabet"),
    theme: readStorage(STORAGE_KEYS.theme, "system"),
    pinned: new Set(readJsonStorage(STORAGE_KEYS.pinned, [])),
    location: { ...DEFAULT_LOCATION, source: "預設位置（南港展覽館）" },
    events: {
        offset: 0,
        total: 0,
        hasMore: true,
        loading: false,
        loadedIds: new Set()
    }
};

/* ------------------------------------------------------------------ */
/* DOM 參照                                                            */
/* ------------------------------------------------------------------ */

const dom = {
    main: document.querySelector("#appMain"),
    title: document.querySelector("#viewTitle"),
    backButton: document.querySelector("#backButton"),
    locationText: document.querySelector("#locationText"),
    settingsLocation: document.querySelector("#settingsLocation"),
    requestLocation: document.querySelector("#requestLocation"),
    parkingList: document.querySelector("#parkingList"),
    parkingStatus: document.querySelector("#parkingStatus"),
    parkingDetail: document.querySelector("#parkingDetail"),
    eventList: document.querySelector("#eventList"),
    eventStatus: document.querySelector("#eventStatus"),
    eventSentinel: document.querySelector("#eventSentinel"),
    startDate: document.querySelector("#startDate"),
    endDate: document.querySelector("#endDate"),
    resetFilter: document.querySelector("#resetFilter"),
    sortToggle: document.querySelector("#sortToggle"),
    weatherTrack: document.querySelector("#weatherTrack"),
    weatherStatus: document.querySelector("#weatherStatus"),
    tabButtons: [...document.querySelectorAll(".tab-button")]
};

const views = {
    parking: document.querySelector("#parkingView"),
    parkingDetail: document.querySelector("#parkingDetailView"),
    events: document.querySelector("#eventsView"),
    weather: document.querySelector("#weatherView"),
    settings: document.querySelector("#settingsView")
};

const viewTitles = {
    parking: "停車場",
    parkingDetail: "停車場詳情",
    events: "活動",
    weather: "天氣",
    settings: "設定"
};

/* ------------------------------------------------------------------ */
/* 共用小工具                                                          */
/* ------------------------------------------------------------------ */

function readStorage(key, fallback) {
    return localStorage.getItem(key) ?? fallback;
}

function readJsonStorage(key, fallback) {
    try {
        return JSON.parse(localStorage.getItem(key)) ?? fallback;
    } catch {
        return fallback;
    }
}

/** 呼叫後端 API 並回傳 JSON，失敗時丟出可讀的錯誤訊息 */
async function requestApi(path, params = {}) {
    const url = new URL(API_BASE + path, location.href);
    Object.entries(params).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== "") {
            url.searchParams.set(key, value);
        }
    });

    const response = await fetch(url, { headers: { Accept: "application/json" } });
    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
        throw new Error(payload.error || `HTTP ${response.status}`);
    }

    return payload;
}

function formatDate(value) {
    return new Date(`${value}T00:00:00`).toLocaleDateString("zh-TW", {
        month: "long",
        day: "numeric",
        weekday: "short"
    });
}

function formatDistance(meters) {
    return meters >= 1000 ? `${(meters / 1000).toFixed(1)} 公里` : `${Math.round(meters)} 公尺`;
}

/** Haversine 公式：計算兩組經緯度之間的地表距離（公尺） */
function distanceBetween(latitude1, longitude1, latitude2, longitude2) {
    const earthRadius = 6371000;
    const toRadian = (degree) => (degree * Math.PI) / 180;
    const deltaLatitude = toRadian(latitude2 - latitude1);
    const deltaLongitude = toRadian(longitude2 - longitude1);
    const a =
        Math.sin(deltaLatitude / 2) ** 2 +
        Math.cos(toRadian(latitude1)) * Math.cos(toRadian(latitude2)) * Math.sin(deltaLongitude / 2) ** 2;

    return earthRadius * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

function distanceToLot(lot) {
    return distanceBetween(state.location.latitude, state.location.longitude, lot.latitude, lot.longitude);
}

/* ------------------------------------------------------------------ */
/* 檢視切換與頁首                                                      */
/* ------------------------------------------------------------------ */

function switchView(nextView) {
    if (!views[nextView]) {
        nextView = "parking";
    }

    Object.values(views).forEach((view) => view.classList.remove("active"));
    views[nextView].classList.add("active");

    state.view = nextView;
    dom.title.textContent = viewTitles[nextView];
    dom.backButton.hidden = nextView !== "parkingDetail";

    dom.tabButtons.forEach((button) => {
        const isActive = button.dataset.view === nextView;
        button.classList.toggle("active", isActive);
        if (isActive) {
            button.setAttribute("aria-current", "page");
        } else {
            button.removeAttribute("aria-current");
        }
    });

    if (nextView !== "parkingDetail") {
        stopDetailRefresh();
    }

    dom.main.scrollTop = 0;
}

function navigate(nextView) {
    if (nextView !== "parkingDetail") {
        state.previousView = nextView;
        location.hash = nextView;
    }
    switchView(nextView);
}

/* ------------------------------------------------------------------ */
/* 地理位置                                                            */
/* ------------------------------------------------------------------ */

/** 依序嘗試：網址查詢參數 → 瀏覽器定位 → 預設位置 */
function resolveLocation() {
    const params = new URLSearchParams(location.search);
    const latitude = Number(params.get("latitude"));
    const longitude = Number(params.get("longitude"));

    if (params.has("latitude") && params.has("longitude") && Number.isFinite(latitude) && Number.isFinite(longitude)) {
        setLocation(latitude, longitude, "URL 查詢參數");
        return;
    }

    requestBrowserLocation();
}

function requestBrowserLocation() {
    if (!navigator.geolocation) {
        renderLocation();
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => setLocation(position.coords.latitude, position.coords.longitude, "瀏覽器目前位置"),
        () => renderLocation(),
        { enableHighAccuracy: true, timeout: 5000, maximumAge: 60000 }
    );
}

function setLocation(latitude, longitude, source) {
    state.location = { latitude, longitude, source };
    renderLocation();
    renderParkingList();
    if (state.view === "parkingDetail") {
        renderParkingDetail();
    }
}

function renderLocation() {
    const { latitude, longitude, source } = state.location;
    const coordinate = `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
    dom.locationText.textContent = coordinate;
    dom.locationText.title = `${source}：${coordinate}`;
    dom.settingsLocation.textContent = `${source}：緯度 ${latitude.toFixed(6)}、經度 ${longitude.toFixed(6)}`;
}

/* ------------------------------------------------------------------ */
/* 停車場                                                              */
/* ------------------------------------------------------------------ */

async function loadParkingLots() {
    try {
        const payload = await requestApi("parking.php");
        state.parkingLots = payload.data;
        dom.parkingStatus.textContent = "";
        renderParkingList();
    } catch (error) {
        dom.parkingStatus.textContent = `停車場資料載入失敗：${error.message}`;
    }
}

/** 置頂的停車場永遠排在最前面，其餘再依目前排序模式排列 */
function sortedParkingLots() {
    return [...state.parkingLots].sort((a, b) => {
        const pinnedA = state.pinned.has(a.id) ? 0 : 1;
        const pinnedB = state.pinned.has(b.id) ? 0 : 1;
        if (pinnedA !== pinnedB) {
            return pinnedA - pinnedB;
        }
        if (state.sortMode === "distance") {
            return distanceToLot(a) - distanceToLot(b);
        }
        return a.name.localeCompare(b.name, "zh-Hant");
    });
}

function availabilityLabel(lot) {
    if (lot.available === 0) {
        return "已滿";
    }
    return lot.available > 80 ? "空位充足" : lot.available > 40 ? "尚有空位" : "車位偏少";
}

function renderParkingList() {
    dom.parkingList.textContent = "";

    sortedParkingLots().forEach((lot) => {
        const isPinned = state.pinned.has(lot.id);

        const item = document.createElement("li");
        item.className = "list-item";

        const card = document.createElement("button");
        card.type = "button";
        card.className = "card";
        card.addEventListener("click", () => openParkingDetail(lot.id));
        card.innerHTML = `
            <span class="card-main">
                <span class="card-title">${isPinned ? "<span class=\"pin-mark\" aria-hidden=\"true\">★</span>" : ""}${lot.name}</span>
                <span class="meta-row">
                    <span>${formatDistance(distanceToLot(lot))}</span>
                    <span>可用 ${lot.available} / ${lot.total} 格</span>
                </span>
                <span class="badge">${availabilityLabel(lot)}</span>
            </span>
        `;

        const pinButton = document.createElement("button");
        pinButton.type = "button";
        pinButton.className = `pin-button${isPinned ? " active" : ""}`;
        pinButton.textContent = isPinned ? "★" : "☆";
        pinButton.setAttribute("aria-pressed", String(isPinned));
        pinButton.setAttribute("aria-label", `${isPinned ? "取消置頂" : "置頂"} ${lot.name}`);
        pinButton.addEventListener("click", () => togglePin(lot.id));

        item.append(card, pinButton);
        dom.parkingList.append(item);
    });
}

function togglePin(id) {
    if (state.pinned.has(id)) {
        state.pinned.delete(id);
    } else {
        state.pinned.add(id);
    }
    localStorage.setItem(STORAGE_KEYS.pinned, JSON.stringify([...state.pinned]));
    renderParkingList();
}

function openParkingDetail(id) {
    state.selectedParkingId = id;
    renderParkingDetail();
    switchView("parkingDetail");
    startDetailRefresh();
    refreshParkingDetail();
}

function renderParkingDetail() {
    const lot = state.parkingLots.find((item) => item.id === state.selectedParkingId);
    if (!lot) {
        return;
    }

    dom.parkingDetail.innerHTML = `
        <h3>${lot.name}</h3>
        <p class="detail-address">${lot.address}</p>
        <dl class="detail-grid">
            <div class="detail-item">
                <dt>距離</dt>
                <dd>${formatDistance(distanceToLot(lot))}</dd>
            </div>
            <div class="detail-item">
                <dt>可用停車位</dt>
                <dd>${lot.available}</dd>
            </div>
            <div class="detail-item">
                <dt>總車位</dt>
                <dd>${lot.total}</dd>
            </div>
            <div class="detail-item">
                <dt>資料更新時間</dt>
                <dd>${new Date().toLocaleTimeString("zh-TW", { hour12: false })}</dd>
            </div>
        </dl>
        <p class="setting-hint">此頁每 ${DETAIL_REFRESH_MS / 1000} 秒自動向後端重新取得最新空位。</p>
    `;
}

/** 詳情頁每 10 秒向後端重新取得該停車場的即時資料 */
function startDetailRefresh() {
    stopDetailRefresh();
    state.detailTimer = window.setInterval(refreshParkingDetail, DETAIL_REFRESH_MS);
}

function stopDetailRefresh() {
    if (state.detailTimer) {
        window.clearInterval(state.detailTimer);
        state.detailTimer = null;
    }
}

async function refreshParkingDetail() {
    if (state.selectedParkingId === null) {
        return;
    }

    try {
        const payload = await requestApi("parking.php", { id: state.selectedParkingId });
        const index = state.parkingLots.findIndex((item) => item.id === payload.data.id);
        if (index >= 0) {
            state.parkingLots[index] = payload.data;
        }
        if (state.view === "parkingDetail") {
            renderParkingDetail();
        }
        renderParkingList();
    } catch {
        /* 忽略單次輪詢失敗，下一次會再嘗試 */
    }
}

/* ------------------------------------------------------------------ */
/* 活動（日期篩選 + 無限捲動）                                          */
/* ------------------------------------------------------------------ */

function resetEvents() {
    state.events.offset = 0;
    state.events.total = 0;
    state.events.hasMore = true;
    state.events.loading = false;
    state.events.loadedIds.clear();
    dom.eventList.textContent = "";
    dom.eventStatus.textContent = "";
    loadMoreEvents();
}

async function loadMoreEvents() {
    if (state.events.loading || !state.events.hasMore) {
        return;
    }

    state.events.loading = true;
    dom.eventStatus.textContent = "載入中…";

    try {
        const payload = await requestApi("events.php", {
            start: dom.startDate.value,
            end: dom.endDate.value,
            offset: state.events.offset,
            limit: EVENT_PAGE_SIZE
        });

        payload.data.forEach((event) => {
            // 以 id 去重，確保無限捲動不會出現重複記錄
            if (state.events.loadedIds.has(event.id)) {
                return;
            }
            state.events.loadedIds.add(event.id);
            dom.eventList.append(renderEventCard(event));
        });

        state.events.offset += payload.data.length;
        state.events.total = payload.total;
        state.events.hasMore = payload.has_more;

        if (payload.total === 0) {
            dom.eventStatus.textContent = "查無符合條件的活動";
        } else if (!state.events.hasMore) {
            dom.eventStatus.textContent = `已載入全部 ${payload.total} 筆活動`;
        } else {
            dom.eventStatus.textContent = `已載入 ${state.events.offset} / ${payload.total} 筆`;
        }
    } catch (error) {
        dom.eventStatus.textContent = `活動資料載入失敗：${error.message}`;
        state.events.hasMore = false;
    } finally {
        state.events.loading = false;
        // 若清單仍未填滿可視範圍，立刻補上下一頁，避免無法觸發捲動
        if (state.events.hasMore && dom.main.scrollHeight <= dom.main.clientHeight + 40 && state.view === "events") {
            loadMoreEvents();
        }
    }
}

function renderEventCard(event) {
    const item = document.createElement("li");
    item.className = "event-card";

    const image = document.createElement("img");
    image.src = event.image_url || eventPlaceholder(event);
    image.alt = "";
    image.loading = "lazy";
    image.width = 800;
    image.height = 400;

    const body = document.createElement("div");
    body.className = "event-card-body";

    const title = document.createElement("h3");
    title.textContent = event.title;

    const date = document.createElement("p");
    date.className = "event-date";
    date.textContent = event.start_date === event.end_date
        ? formatDate(event.start_date)
        : `${formatDate(event.start_date)} － ${formatDate(event.end_date)}`;

    const description = document.createElement("p");
    description.className = "event-description";
    description.textContent = event.description;

    body.append(title, date, description);
    item.append(image, body);

    return item;
}

/** 活動若沒有指定圖片，用主題色即時產生一張 SVG 佔位圖 */
function eventPlaceholder(event) {
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="800" height="400" viewBox="0 0 800 400">
        <rect width="800" height="400" fill="${event.image_color}"/>
        <path d="M0 318 C150 250 220 370 385 292 C552 214 604 326 800 232 L800 400 L0 400 Z" fill="rgba(255,255,255,.18)"/>
        <circle cx="664" cy="98" r="56" fill="rgba(255,255,255,.18)"/>
    </svg>`;

    return `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}`;
}

/** 用 IntersectionObserver 監看清單底部的哨兵元素，捲動到底前先載入下一頁 */
function observeEventSentinel() {
    const observer = new IntersectionObserver(
        (entries) => {
            if (entries.some((entry) => entry.isIntersecting) && state.view === "events") {
                loadMoreEvents();
            }
        },
        { root: dom.main, rootMargin: "240px 0px" }
    );

    observer.observe(dom.eventSentinel);
}

/* ------------------------------------------------------------------ */
/* 天氣                                                                */
/* ------------------------------------------------------------------ */

async function loadWeather() {
    try {
        const payload = await requestApi("weather.php");
        renderWeather(payload.data);
        dom.weatherStatus.textContent = "";
    } catch (error) {
        dom.weatherStatus.textContent = `天氣資料載入失敗：${error.message}`;
    }
}

function renderWeather(days) {
    dom.weatherTrack.textContent = "";

    days.forEach((day) => {
        const item = document.createElement("li");
        item.className = "weather-card";
        item.innerHTML = `
            <div class="weather-head">
                <p class="weather-date">${formatDate(day.date)}</p>
                <h3>${day.condition}</h3>
                <p class="weather-temp">${day.high}°</p>
                <p class="weather-meta">最低 ${day.low}° ・ 降雨機率 ${day.rain_chance}%</p>
            </div>
            ${weatherIconMarkup(day.icon, day.condition)}
        `;
        dom.weatherTrack.append(item);
    });
}

/** 依天氣狀況輸出素材提供的 SVG 圖示（無填色、僅描邊，供 hover 描邊動畫使用） */
function weatherIconMarkup(icon, condition) {
    const paths = WEATHER_ICON_PATHS[icon] || WEATHER_ICON_PATHS.sunny;
    const shapes = paths.map((path) => `<path d="${path}"/>`).join("");

    return `<svg class="weather-svg" viewBox="0 0 96 96" role="img" aria-label="${condition}">
        <title>${condition}</title>
        ${shapes}
    </svg>`;
}

/* ------------------------------------------------------------------ */
/* 設定                                                                */
/* ------------------------------------------------------------------ */

function applyTheme(theme) {
    state.theme = theme;
    document.documentElement.dataset.theme = theme;
    localStorage.setItem(STORAGE_KEYS.theme, theme);

    document.querySelectorAll("input[name='theme']").forEach((input) => {
        input.checked = input.value === theme;
    });

    // 讓瀏覽器 UI 的主題色與目前配色一致
    const dark = theme === "dark" || (theme === "system" && matchMedia("(prefers-color-scheme: dark)").matches);
    document.querySelector("meta[name='theme-color']").content = dark ? "#15191d" : "#f7f5ef";
}

function applySortMode(mode) {
    state.sortMode = mode;
    localStorage.setItem(STORAGE_KEYS.sort, mode);
    dom.sortToggle.checked = mode === "distance";
    dom.sortToggle.setAttribute("aria-checked", String(mode === "distance"));
    dom.sortToggle.setAttribute("aria-label", mode === "distance" ? "目前為按距離排序" : "目前為按字母排序");
    renderParkingList();
}

/* ------------------------------------------------------------------ */
/* 事件綁定與啟動                                                      */
/* ------------------------------------------------------------------ */

function bindEvents() {
    dom.tabButtons.forEach((button) => {
        button.addEventListener("click", () => navigate(button.dataset.view));
    });

    dom.backButton.addEventListener("click", () => navigate(state.previousView || "parking"));

    dom.sortToggle.addEventListener("change", () => {
        applySortMode(dom.sortToggle.checked ? "distance" : "alphabet");
    });

    document.querySelectorAll("input[name='theme']").forEach((input) => {
        input.addEventListener("change", () => applyTheme(input.value));
    });

    dom.requestLocation.addEventListener("click", requestBrowserLocation);

    dom.startDate.addEventListener("change", resetEvents);
    dom.endDate.addEventListener("change", resetEvents);
    dom.resetFilter.addEventListener("click", () => {
        dom.startDate.value = "";
        dom.endDate.value = "";
        resetEvents();
    });

    // 捲動事件作為 IntersectionObserver 的備援
    dom.main.addEventListener("scroll", () => {
        if (state.view !== "events") {
            return;
        }
        const remaining = dom.main.scrollHeight - dom.main.scrollTop - dom.main.clientHeight;
        if (remaining < 240) {
            loadMoreEvents();
        }
    }, { passive: true });

    window.addEventListener("hashchange", () => {
        switchView(location.hash.slice(1) || "parking");
    });

    matchMedia("(prefers-color-scheme: dark)").addEventListener("change", () => {
        if (state.theme === "system") {
            applyTheme("system");
        }
    });
}

function init() {
    applyTheme(state.theme);
    applySortMode(state.sortMode);
    renderLocation();
    bindEvents();
    observeEventSentinel();

    switchView(location.hash.slice(1) || "parking");
    state.previousView = state.view === "parkingDetail" ? "parking" : state.view;

    resolveLocation();
    loadParkingLots();
    resetEvents();
    loadWeather();
}

document.addEventListener("DOMContentLoaded", init);
