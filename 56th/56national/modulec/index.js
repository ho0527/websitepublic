const parkingLots = [
    { id: "p1", name: "南港展覽館一館地下停車場", available: 126, total: 620, latitude: 25.0561, longitude: 121.6178 },
    { id: "p2", name: "南港展覽館二館停車場", available: 74, total: 320, latitude: 25.0581, longitude: 121.6162 },
    { id: "p3", name: "南港車站轉乘停車場", available: 212, total: 540, latitude: 25.0526, longitude: 121.6078 },
    { id: "p4", name: "經貿二路平面停車場", available: 38, total: 96, latitude: 25.0595, longitude: 121.6145 },
    { id: "p5", name: "中信金融園區停車場", available: 91, total: 260, latitude: 25.0592, longitude: 121.6157 },
    { id: "p6", name: "南港軟體園區停車場", available: 57, total: 180, latitude: 25.0572, longitude: 121.6127 }
];

const eventSeed = [
    ["2026 台北國際電腦展", "2026-06-02", "科技產業展示與新品發表", "#1c3e60"],
    ["智慧城市應用展", "2026-06-10", "城市服務、交通與資料應用", "#217453"],
    ["亞洲生技論壇", "2026-06-18", "生技新創、醫療器材與研討會", "#b3542f"],
    ["設計品牌週", "2026-06-24", "生活風格、設計選物與講座", "#8c5a2b"],
    ["國際旅遊展", "2026-07-03", "旅遊產品、航空與城市推廣", "#315f75"],
    ["電動車與能源展", "2026-07-12", "車用科技、充電與儲能方案", "#7c6c2d"],
    ["動漫創作博覽會", "2026-07-20", "IP 展示、舞台活動與簽名會", "#7d4e8a"],
    ["台灣美食嘉年華", "2026-07-27", "地方料理與餐飲品牌展售", "#a05b08"],
    ["資安技術大會", "2026-08-04", "攻防演練、資安治理與雲端安全", "#305c86"],
    ["國際教育展", "2026-08-15", "海外留學、語言學習與學程諮詢", "#675f9b"],
    ["永續材料展", "2026-08-22", "循環材料、綠建材與 ESG 方案", "#2f7557"],
    ["運動健康產業展", "2026-09-02", "健身設備、運動科技與健康管理", "#b45f3b"],
    ["寵物用品博覽會", "2026-09-11", "生活用品、食品與品牌活動", "#7d6848"],
    ["食品加工設備展", "2026-09-19", "智慧製造、包裝與食品安全", "#596d34"],
    ["創新創業媒合日", "2026-09-26", "新創展示、投資媒合與講座", "#465f8d"],
    ["文具禮品採購展", "2026-10-04", "文創商品、商務禮品與通路採購", "#9b536c"],
    ["AI 應用開發日", "2026-10-13", "企業 AI、模型應用與工作坊", "#245b69"],
    ["國際家具家飾展", "2026-10-21", "居家設計、家具與照明品牌", "#6a5739"]
];

const weatherData = [
    { date: "2026-05-29", condition: "晴時多雲", icon: "sunny", high: 31, low: 25, rain: 10 },
    { date: "2026-05-30", condition: "多雲", icon: "cloudy", high: 30, low: 24, rain: 20 },
    { date: "2026-05-31", condition: "短暫雨", icon: "rainy", high: 28, low: 23, rain: 60 },
    { date: "2026-06-01", condition: "多雲", icon: "cloudy", high: 29, low: 24, rain: 30 },
    { date: "2026-06-02", condition: "晴朗", icon: "sunny", high: 32, low: 25, rain: 10 },
    { date: "2026-06-03", condition: "午後雷雨", icon: "rainy", high: 30, low: 24, rain: 70 },
    { date: "2026-06-04", condition: "晴時多雲", icon: "sunny", high: 31, low: 25, rain: 20 }
];

const state = {
    view: location.hash.substring(1)??"parking",
    previousView: null,
    selectedParkingId: null,
    sortMode: localStorage.getItem("modulec-sort") || "alphabet",
    pinned: new Set(JSON.parse(localStorage.getItem("modulec-pinned") || "[]")),
    theme: localStorage.getItem("modulec-theme") || "system",
    location: { latitude: 25.0561, longitude: 121.6178, source: "預設位置" },
    eventPage: 0,
    eventPageSize: 5,
    filteredEvents: [],
    loadingEvents: false,
    detailTimer: null
};

const views = {
    parking: document.querySelector("#parkingView"),
    parkingDetail: document.querySelector("#parkingDetailView"),
    events: document.querySelector("#eventsView"),
    weather: document.querySelector("#weatherView"),
    settings: document.querySelector("#settingsView")
};

const titles = {
    parking: "停車場",
    parkingDetail: "停車場詳情",
    events: "活動",
    weather: "天氣",
    settings: "設定"
};

const main = document.querySelector("#appMain");
const title = document.querySelector("#viewTitle");
const backButton = document.querySelector(".back-button");
const locationText = document.querySelector("#locationText");
const parkingList = document.querySelector("#parkingList");
const parkingDetail = document.querySelector("#parkingDetail");
const eventList = document.querySelector("#eventList");
const eventStatus = document.querySelector("#eventStatus");
const startDate = document.querySelector("#startDate");
const endDate = document.querySelector("#endDate");
const sortToggle = document.querySelector("#sortToggle");
const weatherTrack = document.querySelector("#weatherTrack");

document.addEventListener("DOMContentLoaded", init);

function init() {
    setTheme(state.theme);
    initSettings();
    resolveLocation();
    renderParkingList();
    resetEvents();
    renderWeather();
    bindEvents();
    switchView(state.view);
    registerServiceWorker();
}

function bindEvents() {
    document.querySelectorAll(".tab-button").forEach((button) => {
        button.addEventListener("click", () => {
            switchView(button.dataset.view)
            location.href="#"+button.dataset.view
        });
    });

    backButton.addEventListener("click", () => {
        switchView(state.previousView || "parking");
        location.href="#"+state.previousView
    });

    sortToggle.addEventListener("change", () => {
        state.sortMode = sortToggle.checked ? "distance" : "alphabet";
        localStorage.setItem("modulec-sort", state.sortMode);
        renderParkingList();
    });

    document.querySelectorAll("input[name='theme']").forEach((input) => {
        input.addEventListener("change", () => setTheme(input.value));
    });

    startDate.addEventListener("change", resetEvents);
    endDate.addEventListener("change", resetEvents);
    main.addEventListener("scroll", handleInfiniteScroll, { passive: true });
}

function initSettings() {
    sortToggle.checked = state.sortMode === "distance";
    const themeInput = document.querySelector(`input[name='theme'][value='${state.theme}']`);
    if (themeInput) themeInput.checked = true;
}

function switchView(nextView, options = {}) {
    Object.values(views).forEach((view) => view.classList.remove("active"));
    views[nextView].classList.add("active");
    title.textContent = titles[nextView];
    state.view = nextView;
    backButton.hidden = nextView !== "parkingDetail";
    document.querySelectorAll(".tab-button").forEach((button) => {
        button.classList.toggle("active", button.dataset.view === nextView);
    });
    if (!options.keepScroll) main.scrollTop = 0;
    if (nextView !== "parkingDetail") stopDetailUpdates();
}

function resolveLocation() {
    const params = new URLSearchParams(location.search);
    const queryLatitude = Number(params.get("latitude"));
    const queryLongitude = Number(params.get("longitude"));

    if (Number.isFinite(queryLatitude) && Number.isFinite(queryLongitude)) {
        setLocation(queryLatitude, queryLongitude, "URL 參數");
        return;
    }

    if (!navigator.geolocation) {
        updateLocationText();
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => setLocation(position.coords.latitude, position.coords.longitude, "目前位置"),
        () => updateLocationText(),
        { enableHighAccuracy: true, timeout: 5000, maximumAge: 60000 }
    );
}

function setLocation(latitude, longitude, source) {
    state.location = { latitude, longitude, source };
    updateLocationText();
    renderParkingList();
}

function updateLocationText(){
    return ;
}

function getSortedParkingLots() {
    return [...parkingLots].sort((a, b) => {
        const pinnedA = state.pinned.has(a.id) ? 0 : 1;
        const pinnedB = state.pinned.has(b.id) ? 0 : 1;
        if (pinnedA !== pinnedB) return pinnedA - pinnedB;
        if (state.sortMode === "distance") return getDistance(a) - getDistance(b);
        return a.name.localeCompare(b.name, "zh-Hant");
    });
}

function renderParkingList() {
    parkingList.innerHTML = "";
    getSortedParkingLots().forEach((lot) => {
        const card = document.createElement("article");
        card.className = "card";
        card.tabIndex = 0;
        card.setAttribute("role", "button");
        card.setAttribute("aria-label", `查看 ${lot.name} 詳情`);
        card.innerHTML = `
            <div class="card-main">
                <h3 class="card-title">${lot.name}</h3>
                <div class="meta-row">
                    <span>${formatDistance(getDistance(lot))}</span>
                    <span>${lot.available} / ${lot.total} 格</span>
                </div>
                <span class="badge">${lot.available > 80 ? "空位充足" : lot.available > 40 ? "尚有空位" : "車位偏少"}</span>
            </div>
            <button class="pin-button ${state.pinned.has(lot.id) ? "active" : ""}" type="button" aria-label="${state.pinned.has(lot.id) ? "取消置頂" : "置頂"} ${lot.name}">
                ${state.pinned.has(lot.id) ? "★" : "☆"}
            </button>
        `;
        card.addEventListener("click", () => openParkingDetail(lot.id));
        card.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                openParkingDetail(lot.id);
            }
        });
        card.querySelector(".pin-button").addEventListener("click", (event) => {
            event.stopPropagation();
            togglePin(lot.id);
        });
        parkingList.append(card);
    });
}

function togglePin(id) {
    if (state.pinned.has(id)) {
        state.pinned.delete(id);
    } else {
        state.pinned.add(id);
    }
    localStorage.setItem("modulec-pinned", JSON.stringify([...state.pinned]));
    renderParkingList();
}

function openParkingDetail(id) {
    state.previousView = "parking";
    state.selectedParkingId = id;
    renderParkingDetail();
    switchView("parkingDetail");
    startDetailUpdates();
}

function renderParkingDetail() {
    const lot = parkingLots.find((item) => item.id === state.selectedParkingId);
    if (!lot) return;
    parkingDetail.innerHTML = `
        <h2>${lot.name}</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <span>距離</span>
                <strong>${formatDistance(getDistance(lot))}</strong>
            </div>
            <div class="detail-item">
                <span>可用停車位</span>
                <strong>${lot.available}</strong>
            </div>
            <div class="detail-item">
                <span>總車位</span>
                <strong>${lot.total}</strong>
            </div>
            <div class="detail-item">
                <span>更新時間</span>
                <strong>${new Date().toLocaleTimeString("zh-TW", { hour: "2-digit", minute: "2-digit", second: "2-digit" })}</strong>
            </div>
        </div>
    `;
}

function startDetailUpdates() {
    stopDetailUpdates();
    state.detailTimer = window.setInterval(() => {
        const lot = parkingLots.find((item) => item.id === state.selectedParkingId);
        if (!lot) return;
        const delta = Math.floor(Math.random() * 7) - 3;
        lot.available = Math.max(0, Math.min(lot.total, lot.available + delta));
        renderParkingDetail();
        renderParkingList();
    }, 10000);
}

function stopDetailUpdates() {
    if (state.detailTimer) {
        clearInterval(state.detailTimer);
        state.detailTimer = null;
    }
}

function getDistance(lot) {
    return distanceBetween(state.location.latitude, state.location.longitude, lot.latitude, lot.longitude);
}

function distanceBetween(lat1, lon1, lat2, lon2) {
    const earthRadius = 6371000;
    const toRad = (degree) => degree * Math.PI / 180;
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a = Math.sin(dLat / 2) ** 2 +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
        Math.sin(dLon / 2) ** 2;
    return earthRadius * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

function formatDistance(meters) {
    return meters >= 1000 ? `${(meters / 1000).toFixed(1)} km` : `${Math.round(meters)} m`;
}

function buildEvents() {
    return eventSeed.map(([titleText, date, description, color], index) => ({
        id: `e${index + 1}`,
        title: titleText,
        date,
        description,
        image: makeEventImage(titleText, date, color)
    }));
}

function resetEvents() {
    state.filteredEvents = buildEvents().filter((event) => {
        const afterStart = !startDate.value || event.date >= startDate.value;
        const beforeEnd = !endDate.value || event.date <= endDate.value;
        return afterStart && beforeEnd;
    });
    state.eventPage = 0;
    eventList.innerHTML = "";
    eventStatus.textContent = "";
    loadMoreEvents();
}

function handleInfiniteScroll() {
    if (state.view !== "events" || state.loadingEvents) return;
    const distanceToBottom = main.scrollHeight - main.scrollTop - main.clientHeight;
    if (distanceToBottom < 220) loadMoreEvents();
}

function loadMoreEvents() {
    if (state.loadingEvents) return;
    const start = state.eventPage * state.eventPageSize;
    const batch = state.filteredEvents.slice(start, start + state.eventPageSize);

    if (batch.length === 0) {
        eventStatus.textContent = state.filteredEvents.length ? "已載入全部活動" : "查無符合條件的活動";
        return;
    }

    state.loadingEvents = true;
    eventStatus.textContent = "載入中";
    window.setTimeout(() => {
        batch.forEach((event) => eventList.append(renderEventCard(event)));
        state.eventPage += 1;
        state.loadingEvents = false;
        eventStatus.textContent = state.eventPage * state.eventPageSize >= state.filteredEvents.length ? "已載入全部活動" : "";
    }, 180);
}

function renderEventCard(event) {
    const article = document.createElement("article");
    article.className = "event-card";
    article.innerHTML = `
        <img src="${event.image}" alt="">
        <div class="event-card-body">
            <h3>${event.title}</h3>
            <div class="event-date">${formatDate(event.date)}</div>
            <p>${event.description}</p>
        </div>
    `;
    return article;
}

function makeEventImage(titleText, date, color) {
    const svg = `
        <svg xmlns="http://www.w3.org/2000/svg" width="800" height="400" viewBox="0 0 800 400">
            <rect width="800" height="400" fill="${color}"/>
            <path d="M0 318 C150 250 220 370 385 292 C552 214 604 326 800 232 L800 400 L0 400 Z" fill="rgba(255,255,255,.18)"/>
            <circle cx="664" cy="98" r="56" fill="rgba(255,255,255,.18)"/>
            <text x="48" y="250" fill="#fff" font-family="Microsoft JhengHei, sans-serif" font-size="42" font-weight="700">${escapeSvg(titleText)}</text>
            <text x="52" y="306" fill="rgba(255,255,255,.82)" font-family="Arial, sans-serif" font-size="28">${date}</text>
        </svg>
    `;
    return `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}`;
}

function renderWeather() {
    weatherTrack.innerHTML = "";
    weatherData.forEach((day) => {
        const article = document.createElement("article");
        article.className = "weather-card";
        article.tabIndex = 0;
        article.innerHTML = `
            <div>
                <div class="weather-date">${formatDate(day.date)}</div>
                <h3>${day.condition}</h3>
                <div class="weather-temp">${day.high}°</div>
                <p>${day.low}° 低溫 · 降雨 ${day.rain}%</p>
            </div>
            ${weatherIcon(day.icon)}
        `;
        weatherTrack.append(article);
    });
}

function weatherIcon(type) {
    const common = `class="weather-svg" viewBox="0 0 120 120" aria-hidden="true" focusable="false"`;
    if (type === "rainy") {
        return `<svg ${common}>
            <path d="M35 72h48a18 18 0 0 0 1-36 26 26 0 0 0-48-8 22 22 0 0 0-1 44Z"/>
            <path d="M42 84l-8 16M62 84l-8 16M82 84l-8 16"/>
        </svg>`;
    }
    if (type === "cloudy") {
        return `<svg ${common}>
            <path d="M28 78h58a20 20 0 0 0 2-40 30 30 0 0 0-56-8 24 24 0 0 0-4 48Z"/>
            <path d="M22 92h72"/>
        </svg>`;
    }
    return `<svg ${common}>
        <circle cx="60" cy="60" r="22"/>
        <path d="M60 12v18M60 90v18M12 60h18M90 60h18M26 26l13 13M81 81l13 13M94 26 81 39M39 81 26 94"/>
    </svg>`;
}

function setTheme(theme) {
    state.theme = theme;
    document.documentElement.dataset.theme = theme;
    localStorage.setItem("modulec-theme", theme);
    document.querySelectorAll("input[name='theme']").forEach((input) => {
        input.checked = input.value === theme;
    });
}

function formatDate(value) {
    return new Date(`${value}T00:00:00`).toLocaleDateString("zh-TW", {
        month: "long",
        day: "numeric",
        weekday: "short"
    });
}

function escapeSvg(text) {
    return text.replace(/[&<>"']/g, (char) => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        "\"": "&quot;",
        "'": "&apos;"
    }[char]));
}

function registerServiceWorker() {
    if ("serviceWorker" in navigator && location.protocol !== "file:") {
        navigator.serviceWorker.register("sw.js").catch(() => {});
    }
}
