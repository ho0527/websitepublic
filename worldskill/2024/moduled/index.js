/* =========================================================================
 * WorldSkills 2024 - 模組 D - Lyon Mobile Web Service
 *
 * 程式以「模組化物件」的方式組織，每一區塊各自負責一件事：
 *   apiClient        - 與模擬 API 伺服器溝通
 *   geolocationModule- 取得（或模擬）目前位置
 *   carparkModule    - 停車場列表、排序、置頂、單一停車場檢視
 *   eventModule      - 活動列表、日期查詢、無限捲動
 *   weatherModule    - 一週天氣、水平貼齊捲動、SVG 圖示
 *   settingModule    - 深/淺色主題與排序方式設定
 *   viewRouter       - 檢視切換與頁首標題
 * ========================================================================= */

(function () {
	"use strict";

	/* ---------------------------------------------------------------------
	 * 常數設定
	 * ------------------------------------------------------------------- */

	/** 模擬 API 伺服器的位置（與本頁同目錄） */
	const API_SCRIPT_PATH = "module_d_api.php";

	/** localStorage 使用的鍵名前綴，避免與同網域其他專案衝突 */
	const STORAGE_PREFIX = "wsc2024-module-d-";

	/** 置頂停車場、主題、排序方式的儲存鍵 */
	const STORAGE_KEY_PINNED_CARPARKS = STORAGE_PREFIX + "pinned-carparks";
	const STORAGE_KEY_THEME_MODE = STORAGE_PREFIX + "theme-mode";
	const STORAGE_KEY_CARPARK_SORT = STORAGE_PREFIX + "carpark-sort";

	/** 單一停車場檢視的空位重新整理間隔（毫秒） */
	const FOCUS_REFRESH_INTERVAL_MS = 10000;

	/** 無限捲動預先載入的距離（像素），數值越大載入越早 */
	const INFINITE_SCROLL_PRELOAD_MARGIN_PX = 500;

	/** 地球平均半徑（公里），用於計算兩個經緯度之間的距離 */
	const EARTH_RADIUS_KM = 6371;

	/** 預設位置：里昂市中心（白鵝廣場），在取不到定位時使用 */
	const DEFAULT_LOCATION = { latitude: 45.757430, longitude: 4.832160 };

	/* ---------------------------------------------------------------------
	 * 共用工具函式
	 * ------------------------------------------------------------------- */

	/**
	 * 依 id 取得元素的簡寫。
	 * @param {string} elementId 元素 id
	 * @returns {HTMLElement|null}
	 */
	function byId(elementId) {
		return document.getElementById(elementId);
	}

	/**
	 * 讀取 localStorage 的 JSON 值，解析失敗時回傳預設值。
	 * @param {string} storageKey  儲存鍵
	 * @param {*}      defaultValue 預設值
	 */
	function readStoredValue(storageKey, defaultValue) {
		try {
			const rawValue = window.localStorage.getItem(storageKey);
			return rawValue === null ? defaultValue : JSON.parse(rawValue);
		} catch (error) {
			return defaultValue;
		}
	}

	/**
	 * 將值以 JSON 形式寫入 localStorage（隱私模式下失敗時安靜略過）。
	 * @param {string} storageKey 儲存鍵
	 * @param {*}      value      要儲存的值
	 */
	function writeStoredValue(storageKey, value) {
		try {
			window.localStorage.setItem(storageKey, JSON.stringify(value));
		} catch (error) {
			/* 無法寫入時不影響其他功能 */
		}
	}

	/**
	 * 以 Haversine 公式計算兩個經緯度座標之間的距離。
	 * （題目說明此段距離計算程式碼由主辦方提供，此處以標準 Haversine 實作）
	 * @param {number} fromLatitude  起點緯度
	 * @param {number} fromLongitude 起點經度
	 * @param {number} toLatitude    終點緯度
	 * @param {number} toLongitude   終點經度
	 * @returns {number} 距離（公里）
	 */
	function calculateDistanceInKilometres(fromLatitude, fromLongitude, toLatitude, toLongitude) {
		const toRadians = (degree) => (degree * Math.PI) / 180;
		const latitudeDelta = toRadians(toLatitude - fromLatitude);
		const longitudeDelta = toRadians(toLongitude - fromLongitude);

		const haversine =
			Math.sin(latitudeDelta / 2) * Math.sin(latitudeDelta / 2) +
			Math.cos(toRadians(fromLatitude)) *
				Math.cos(toRadians(toLatitude)) *
				Math.sin(longitudeDelta / 2) *
				Math.sin(longitudeDelta / 2);

		return EARTH_RADIUS_KM * 2 * Math.atan2(Math.sqrt(haversine), Math.sqrt(1 - haversine));
	}

	/**
	 * 把距離（公里）格式化成易讀文字。
	 * @param {number|null} distanceInKilometres 距離
	 */
	function formatDistance(distanceInKilometres) {
		if (typeof distanceInKilometres !== "number" || Number.isNaN(distanceInKilometres)) {
			return "Distance unavailable";
		}
		if (distanceInKilometres < 1) {
			return Math.round(distanceInKilometres * 1000) + " m away";
		}
		return distanceInKilometres.toFixed(2) + " km away";
	}

	/**
	 * 建立元素並一次設定文字、class 與屬性。
	 * @param {string} tagName    標籤名稱
	 * @param {object} options    { className, textContent, attributes }
	 * @returns {HTMLElement}
	 */
	function createElement(tagName, options) {
		const element = document.createElement(tagName);
		const settings = options || {};

		if (settings.className) {
			element.className = settings.className;
		}
		if (settings.textContent !== undefined) {
			/* 一律使用 textContent 寫入 API 資料，天然避免 XSS */
			element.textContent = settings.textContent;
		}
		if (settings.attributes) {
			Object.keys(settings.attributes).forEach(function (attributeName) {
				element.setAttribute(attributeName, settings.attributes[attributeName]);
			});
		}
		return element;
	}

	/* ---------------------------------------------------------------------
	 * apiClient：與模擬 API 伺服器溝通
	 * ------------------------------------------------------------------- */
	const apiClient = {
		/**
		 * 組出 API 網址。
		 * 規格上的形式為 module_d_api.php/events.json，
		 * 但本機 nginx 不會把 .php 後面的路徑交給 PHP，故改用等效的查詢字串形式。
		 * @param {string} resourceName 例如 "events.json"
		 * @param {object} [queryParameters] 額外查詢參數
		 * @returns {string}
		 */
		buildUrl: function (resourceName, queryParameters) {
			const url = new URL(API_SCRIPT_PATH, window.location.href);
			url.searchParams.set("path", resourceName);

			Object.keys(queryParameters || {}).forEach(function (parameterName) {
				const parameterValue = queryParameters[parameterName];
				if (parameterValue !== undefined && parameterValue !== null && parameterValue !== "") {
					url.searchParams.set(parameterName, parameterValue);
				}
			});
			return url.toString();
		},

		/**
		 * 取得 JSON 資料，HTTP 失敗時丟出例外。
		 * @param {string} requestUrl 完整網址
		 * @returns {Promise<object>}
		 */
		fetchJson: async function (requestUrl) {
			const response = await fetch(requestUrl, { headers: { Accept: "application/json" } });
			if (!response.ok) {
				throw new Error("API request failed: " + response.status);
			}
			return response.json();
		},

		/** 取得停車場列表 */
		getCarparks: function () {
			return apiClient.fetchJson(apiClient.buildUrl("carparks.json"));
		},

		/**
		 * 取得活動列表（第一頁）。
		 * @param {string} beginningDate 起始日期 YYYY-MM-DD（可空字串）
		 * @param {string} endingDate    結束日期 YYYY-MM-DD（可空字串）
		 */
		getEvents: function (beginningDate, endingDate) {
			return apiClient.fetchJson(
				apiClient.buildUrl("events.json", {
					beginning_date: beginningDate,
					ending_date: endingDate
				})
			);
		},

		/** 依 API 回傳的 pages.next 取得下一頁 */
		getEventsByUrl: function (pageUrl) {
			return apiClient.fetchJson(new URL(pageUrl, window.location.href).toString());
		},

		/** 取得一週天氣 */
		getWeather: function () {
			return apiClient.fetchJson(apiClient.buildUrl("weather.json"));
		}
	};

	/* ---------------------------------------------------------------------
	 * geolocationModule：目前位置（支援網址參數模擬）
	 * ------------------------------------------------------------------- */
	const geolocationModule = {
		/** 目前使用的座標 */
		currentPosition: { latitude: DEFAULT_LOCATION.latitude, longitude: DEFAULT_LOCATION.longitude },

		/** 取得座標的來源說明，會顯示在畫面上 */
		positionSource: "Default location (Lyon city centre)",

		/** 位置更新後要通知的訂閱者 */
		changeListeners: [],

		/**
		 * 註冊位置變更的監聽器。
		 * @param {Function} listener 位置變更時呼叫
		 */
		onChange: function (listener) {
			geolocationModule.changeListeners.push(listener);
		},

		/** 通知所有監聽器位置已更新 */
		notifyChange: function () {
			geolocationModule.changeListeners.forEach(function (listener) {
				listener(geolocationModule.currentPosition);
			});
		},

		/**
		 * 讀取網址中的 latitude / longitude 查詢參數。
		 * 例如 ?latitude=45.755051&longitude=4.846358
		 * @returns {boolean} 是否成功套用網址指定的座標
		 */
		applyPositionFromUrlQuery: function () {
			const queryParameters = new URLSearchParams(window.location.search);
			const latitude = Number.parseFloat(queryParameters.get("latitude"));
			const longitude = Number.parseFloat(queryParameters.get("longitude"));

			if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
				return false;
			}

			geolocationModule.currentPosition = { latitude: latitude, longitude: longitude };
			geolocationModule.positionSource = "Simulated location from URL query";
			geolocationModule.notifyChange();
			return true;
		},

		/**
		 * 向瀏覽器要求目前位置（預設方式）。
		 * 若被瀏覽器安全政策阻擋或使用者拒絕，保留原本的預設座標。
		 */
		requestBrowserPosition: function () {
			if (!("geolocation" in navigator)) {
				geolocationModule.positionSource = "Geolocation is not supported by this browser";
				geolocationModule.notifyChange();
				return;
			}

			navigator.geolocation.getCurrentPosition(
				function (position) {
					geolocationModule.currentPosition = {
						latitude: position.coords.latitude,
						longitude: position.coords.longitude
					};
					geolocationModule.positionSource = "Current device location";
					geolocationModule.notifyChange();
				},
				function (positionError) {
					geolocationModule.positionSource =
						"Location unavailable (" + positionError.message + "), using Lyon city centre";
					geolocationModule.notifyChange();
				},
				{ enableHighAccuracy: true, timeout: 8000, maximumAge: 60000 }
			);
		},

		/** 初始化：網址參數優先，其次向瀏覽器要求定位 */
		initialise: function () {
			if (!geolocationModule.applyPositionFromUrlQuery()) {
				geolocationModule.requestBrowserPosition();
			}
		},

		/** 產生顯示用的座標說明文字 */
		describeCurrentPosition: function () {
			return (
				geolocationModule.positionSource +
				" — latitude " +
				geolocationModule.currentPosition.latitude.toFixed(6) +
				", longitude " +
				geolocationModule.currentPosition.longitude.toFixed(6)
			);
		}
	};

	/* ---------------------------------------------------------------------
	 * settingModule：主題與排序方式
	 * ------------------------------------------------------------------- */
	const settingModule = {
		/** 主題模式："light" | "dark" | "system" */
		themeMode: readStoredValue(STORAGE_KEY_THEME_MODE, "system"),

		/** 停車場排序方式："alphabet" | "distance" */
		carparkSortMethod: readStoredValue(STORAGE_KEY_CARPARK_SORT, "alphabet"),

		/** 監聽系統深/淺色設定的 media query */
		systemThemeQuery: window.matchMedia("(prefers-color-scheme: dark)"),

		/**
		 * 依目前的主題模式套用 <html data-theme>。
		 */
		applyTheme: function () {
			const isSystemDark = settingModule.systemThemeQuery.matches;
			const effectiveTheme =
				settingModule.themeMode === "system" ? (isSystemDark ? "dark" : "light") : settingModule.themeMode;

			document.documentElement.setAttribute("data-theme", effectiveTheme);
		},

		/**
		 * 設定主題模式並保存。
		 * @param {string} themeMode "light" | "dark" | "system"
		 */
		setThemeMode: function (themeMode) {
			settingModule.themeMode = themeMode;
			writeStoredValue(STORAGE_KEY_THEME_MODE, themeMode);
			settingModule.applyTheme();
		},

		/**
		 * 設定排序方式並保存，同時重新繪製停車場列表與工具列。
		 * @param {string} sortMethod "alphabet" | "distance"
		 */
		setCarparkSortMethod: function (sortMethod) {
			settingModule.carparkSortMethod = sortMethod;
			writeStoredValue(STORAGE_KEY_CARPARK_SORT, sortMethod);
			settingModule.syncControls();
			carparkModule.renderList();
		},

		/** 讓設定畫面與停車場工具列的控制項反映目前設定 */
		syncControls: function () {
			document.querySelectorAll('input[name="themeMode"]').forEach(function (radioInput) {
				radioInput.checked = radioInput.value === settingModule.themeMode;
			});
			document.querySelectorAll('input[name="carparkSort"]').forEach(function (radioInput) {
				radioInput.checked = radioInput.value === settingModule.carparkSortMethod;
			});
			document.querySelectorAll("[data-sort-value]").forEach(function (toggleButton) {
				const isSelected = toggleButton.dataset.sortValue === settingModule.carparkSortMethod;
				toggleButton.setAttribute("aria-pressed", isSelected ? "true" : "false");
			});

			const locationNote = byId("settingLocationNote");
			if (locationNote) {
				locationNote.textContent = geolocationModule.describeCurrentPosition();
			}
		},

		/** 綁定設定畫面的事件 */
		initialise: function () {
			settingModule.applyTheme();

			/* 系統主題變動時，只有「跟隨系統」模式需要重新套用 */
			settingModule.systemThemeQuery.addEventListener("change", function () {
				if (settingModule.themeMode === "system") {
					settingModule.applyTheme();
				}
			});

			document.querySelectorAll('input[name="themeMode"]').forEach(function (radioInput) {
				radioInput.addEventListener("change", function () {
					settingModule.setThemeMode(radioInput.value);
				});
			});

			document.querySelectorAll('input[name="carparkSort"]').forEach(function (radioInput) {
				radioInput.addEventListener("change", function () {
					settingModule.setCarparkSortMethod(radioInput.value);
				});
			});

			/* 停車場檢視上方的快速切換鈕 */
			document.querySelectorAll("[data-sort-value]").forEach(function (toggleButton) {
				toggleButton.addEventListener("click", function () {
					settingModule.setCarparkSortMethod(toggleButton.dataset.sortValue);
				});
			});

			settingModule.syncControls();
		}
	};

	/* ---------------------------------------------------------------------
	 * carparkModule：停車場列表 / 排序 / 置頂 / 單一停車場檢視
	 * ------------------------------------------------------------------- */
	const carparkModule = {
		/** API 回傳的停車場資料 */
		carparks: [],

		/** 被置頂的停車場 id 清單（存於 localStorage） */
		pinnedCarparkIds: readStoredValue(STORAGE_KEY_PINNED_CARPARKS, []),

		/** 目前於單一停車場檢視中顯示的停車場 id */
		focusedCarparkId: null,

		/** 單一停車場檢視的自動更新計時器 */
		focusRefreshTimerId: null,

		/**
		 * 從 API 載入停車場資料。
		 * @param {boolean} [silent] 為 true 時不顯示載入狀態文字
		 */
		loadCarparks: async function (silent) {
			try {
				const responseData = await apiClient.getCarparks();
				carparkModule.carparks = Array.isArray(responseData.carparks) ? responseData.carparks : [];
				carparkModule.renderList();
				carparkModule.renderFocusView();
			} catch (error) {
				if (!silent) {
					const emptyHint = byId("carparkEmptyHint");
					emptyHint.textContent = "Unable to load carpark data.";
					emptyHint.hidden = false;
				}
			}
		},

		/**
		 * 判斷停車場是否被置頂。
		 * @param {string} carparkId 停車場 id
		 */
		isPinned: function (carparkId) {
			return carparkModule.pinnedCarparkIds.indexOf(carparkId) !== -1;
		},

		/**
		 * 切換置頂狀態並寫入 localStorage。
		 * @param {string} carparkId 停車場 id
		 */
		togglePinned: function (carparkId) {
			if (carparkModule.isPinned(carparkId)) {
				carparkModule.pinnedCarparkIds = carparkModule.pinnedCarparkIds.filter(function (pinnedId) {
					return pinnedId !== carparkId;
				});
			} else {
				carparkModule.pinnedCarparkIds = carparkModule.pinnedCarparkIds.concat(carparkId);
			}
			writeStoredValue(STORAGE_KEY_PINNED_CARPARKS, carparkModule.pinnedCarparkIds);
			carparkModule.renderList();
		},

		/**
		 * 計算某停車場與目前位置的距離。
		 * @param {object} carpark 停車場資料
		 * @returns {number} 公里
		 */
		getDistanceOf: function (carpark) {
			return calculateDistanceInKilometres(
				geolocationModule.currentPosition.latitude,
				geolocationModule.currentPosition.longitude,
				Number(carpark.latitude),
				Number(carpark.longitude)
			);
		},

		/**
		 * 依目前設定排序，並且不論排序方式，置頂的停車場永遠排在最前面。
		 * @returns {Array<object>} 排序後的停車場陣列
		 */
		getSortedCarparks: function () {
			const sortedCarparks = carparkModule.carparks.slice();

			sortedCarparks.sort(function (leftCarpark, rightCarpark) {
				const leftPinned = carparkModule.isPinned(leftCarpark.id);
				const rightPinned = carparkModule.isPinned(rightCarpark.id);

				/* 置頂優先 */
				if (leftPinned !== rightPinned) {
					return leftPinned ? -1 : 1;
				}

				if (settingModule.carparkSortMethod === "distance") {
					return carparkModule.getDistanceOf(leftCarpark) - carparkModule.getDistanceOf(rightCarpark);
				}
				return String(leftCarpark.name).localeCompare(String(rightCarpark.name), "en");
			});

			return sortedCarparks;
		},

		/**
		 * 依空位比例決定數字顏色的 class。
		 * @param {object} carpark 停車場資料
		 */
		getAvailabilityStateClass: function (carpark) {
			const available = Number(carpark.available);
			const capacity = Number(carpark.capacity) || 1;

			if (available <= 0) {
				return " is-full";
			}
			if (available / capacity < 0.1) {
				return " is-low";
			}
			return "";
		},

		/** 建立單一停車場的列表項目 */
		buildCarparkListItem: function (carpark) {
			const isPinned = carparkModule.isPinned(carpark.id);
			const listItem = createElement("li", {
				className: "carpark-item" + (isPinned ? " is-pinned" : "")
			});

			/* 主要按鈕：點擊後進入單一停車場檢視 */
			const openButton = createElement("button", {
				className: "carpark-open-button",
				attributes: { type: "button" }
			});

			const textWrapper = createElement("span");
			textWrapper.appendChild(createElement("span", { className: "carpark-name", textContent: carpark.name }));
			textWrapper.appendChild(
				createElement("span", {
					className: "carpark-meta",
					textContent: formatDistance(carparkModule.getDistanceOf(carpark)) + " · " + carpark.address
				})
			);

			const availabilityWrapper = createElement("span", { className: "carpark-availability" });
			availabilityWrapper.appendChild(
				createElement("span", {
					className: "carpark-availability-number" + carparkModule.getAvailabilityStateClass(carpark),
					textContent: String(carpark.available)
				})
			);
			availabilityWrapper.appendChild(
				createElement("span", { className: "carpark-availability-unit", textContent: "available" })
			);

			openButton.appendChild(textWrapper);
			openButton.appendChild(availabilityWrapper);
			openButton.addEventListener("click", function () {
				carparkModule.openFocusView(carpark.id);
			});

			/* 置頂 / 取消置頂按鈕 */
			const pinButton = createElement("button", {
				className: "carpark-pin-button",
				attributes: {
					type: "button",
					"aria-pressed": isPinned ? "true" : "false",
					"aria-label": (isPinned ? "Unpin " : "Pin ") + carpark.name + " to top"
				}
			});
			pinButton.innerHTML =
				'<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">' +
				'<path d="M9 3h6l-1 6 4 3v2H6v-2l4-3-1-6z"></path><path d="M12 14v7"></path></svg>';
			pinButton.addEventListener("click", function () {
				carparkModule.togglePinned(carpark.id);
			});

			listItem.appendChild(openButton);
			listItem.appendChild(pinButton);
			return listItem;
		},

		/** 重新繪製停車場列表 */
		renderList: function () {
			const listElement = byId("carparkList");
			const emptyHint = byId("carparkEmptyHint");
			if (!listElement) {
				return;
			}

			listElement.textContent = "";
			const sortedCarparks = carparkModule.getSortedCarparks();
			emptyHint.hidden = sortedCarparks.length > 0;

			const fragment = document.createDocumentFragment();
			sortedCarparks.forEach(function (carpark) {
				fragment.appendChild(carparkModule.buildCarparkListItem(carpark));
			});
			listElement.appendChild(fragment);

			carparkModule.renderLocationBar();
		},

		/** 更新畫面上顯示的目前座標資訊 */
		renderLocationBar: function () {
			const locationBar = byId("locationBar");
			if (locationBar) {
				locationBar.textContent = geolocationModule.describeCurrentPosition();
			}
		},

		/**
		 * 進入單一停車場檢視。
		 * @param {string} carparkId 停車場 id
		 */
		openFocusView: function (carparkId) {
			carparkModule.focusedCarparkId = carparkId;
			viewRouter.showView("carparkFocus");
			carparkModule.renderFocusView();
			carparkModule.startFocusRefreshTimer();
		},

		/** 離開單一停車場檢視 */
		closeFocusView: function () {
			carparkModule.focusedCarparkId = null;
			carparkModule.stopFocusRefreshTimer();
			viewRouter.showView("carparks");
		},

		/** 只顯示名稱、距離與空位數（依規格） */
		renderFocusView: function () {
			if (!carparkModule.focusedCarparkId) {
				return;
			}
			const focusedCarpark = carparkModule.carparks.find(function (carpark) {
				return carpark.id === carparkModule.focusedCarparkId;
			});
			if (!focusedCarpark) {
				return;
			}

			byId("carparkFocusName").textContent = focusedCarpark.name;
			byId("carparkFocusDistance").textContent = formatDistance(carparkModule.getDistanceOf(focusedCarpark));
			byId("carparkFocusAvailability").textContent = String(focusedCarpark.available);
			viewRouter.setHeaderTitle(focusedCarpark.name);
		},

		/** 每 10 秒重新向 API 取得最新空位數 */
		startFocusRefreshTimer: function () {
			carparkModule.stopFocusRefreshTimer();
			carparkModule.focusRefreshTimerId = window.setInterval(function () {
				carparkModule.loadCarparks(true);
			}, FOCUS_REFRESH_INTERVAL_MS);
		},

		/** 停止自動更新，避免離開檢視後仍持續請求 */
		stopFocusRefreshTimer: function () {
			if (carparkModule.focusRefreshTimerId !== null) {
				window.clearInterval(carparkModule.focusRefreshTimerId);
				carparkModule.focusRefreshTimerId = null;
			}
		},

		/** 初始化 */
		initialise: function () {
			byId("headerBackButton").addEventListener("click", function () {
				carparkModule.closeFocusView();
			});

			/* 位置變更時（網址參數或瀏覽器定位），距離與排序需要重算 */
			geolocationModule.onChange(function () {
				carparkModule.renderList();
				carparkModule.renderFocusView();
				settingModule.syncControls();
			});

			carparkModule.loadCarparks(false);
		}
	};

	/* ---------------------------------------------------------------------
	 * eventModule：活動列表 / 日期查詢 / 無限捲動
	 * ------------------------------------------------------------------- */
	const eventModule = {
		/** 下一頁的網址，null 代表已無更多資料 */
		nextPageUrl: null,

		/** 是否正在請求資料，避免無限捲動重複觸發 */
		isLoading: false,

		/** 已顯示過的活動 id，用來確保不重複顯示 */
		displayedEventIds: new Set(),

		/** 觀察哨兵元素的 IntersectionObserver */
		sentinelObserver: null,

		/**
		 * 依目前的日期條件重新查詢（清空列表後重載第一頁）。
		 */
		reloadWithCurrentFilter: async function () {
			const beginningDate = byId("eventBeginningDate").value;
			const endingDate = byId("eventEndingDate").value;

			eventModule.displayedEventIds.clear();
			byId("eventList").textContent = "";
			byId("eventEmptyHint").hidden = true;
			eventModule.nextPageUrl = null;
			eventModule.isLoading = true;
			byId("eventLoadingHint").hidden = false;

			try {
				const responseData = await apiClient.getEvents(beginningDate, endingDate);
				eventModule.appendEvents(responseData);
			} catch (error) {
				byId("eventLoadingHint").textContent = "Unable to load event data.";
			} finally {
				eventModule.isLoading = false;
			}

			/* 首屏若還沒填滿，立即補下一頁，讓捲動有內容可觸發 */
			eventModule.loadNextPageIfViewportNotFilled();
		},

		/**
		 * 載入下一頁（無限捲動）。
		 */
		loadNextPage: async function () {
			if (eventModule.isLoading || !eventModule.nextPageUrl) {
				return;
			}
			eventModule.isLoading = true;
			byId("eventLoadingHint").hidden = false;

			try {
				const responseData = await apiClient.getEventsByUrl(eventModule.nextPageUrl);
				eventModule.appendEvents(responseData);
			} catch (error) {
				byId("eventLoadingHint").textContent = "Unable to load more events.";
			} finally {
				eventModule.isLoading = false;
			}
		},

		/**
		 * 把 API 回傳的活動加入列表，並更新下一頁網址。
		 * @param {object} responseData API 回應
		 */
		appendEvents: function (responseData) {
			const eventRecords = Array.isArray(responseData.events) ? responseData.events : [];
			const listElement = byId("eventList");
			const fragment = document.createDocumentFragment();

			eventRecords.forEach(function (eventRecord) {
				/* 以 id 去除重複，確保不會出現同一筆資料兩次 */
				if (eventModule.displayedEventIds.has(eventRecord.id)) {
					return;
				}
				eventModule.displayedEventIds.add(eventRecord.id);
				fragment.appendChild(eventModule.buildEventListItem(eventRecord));
			});
			listElement.appendChild(fragment);

			eventModule.nextPageUrl = (responseData.pages && responseData.pages.next) || null;
			byId("eventLoadingHint").hidden = eventModule.nextPageUrl === null;
			byId("eventEmptyHint").hidden = eventModule.displayedEventIds.size > 0;
		},

		/** 建立單一活動的列表項目 */
		buildEventListItem: function (eventRecord) {
			const listItem = createElement("li", { className: "event-item" });

			const image = createElement("img", {
				className: "event-image",
				attributes: {
					src: encodeURI(eventRecord.image),
					alt: "Photo of " + eventRecord.title,
					loading: "lazy",
					decoding: "async",
					width: "88",
					height: "64"
				}
			});

			const textWrapper = createElement("div");
			textWrapper.appendChild(createElement("h3", { className: "event-title", textContent: eventRecord.title }));
			textWrapper.appendChild(
				createElement("p", {
					className: "event-date",
					textContent: eventRecord.date + " " + eventRecord.start_time + "–" + eventRecord.end_time
				})
			);
			textWrapper.appendChild(
				createElement("p", {
					className: "event-venue",
					textContent: eventRecord.venue + " · " + eventRecord.category
				})
			);

			listItem.appendChild(image);
			listItem.appendChild(textWrapper);
			return listItem;
		},

		/**
		 * 若目前內容不足以產生捲軸，直接再載一頁，
		 * 避免使用者「捲不動也載不到資料」。
		 */
		loadNextPageIfViewportNotFilled: function () {
			const mainElement = byId("appMain");
			if (
				!eventModule.isLoading &&
				eventModule.nextPageUrl &&
				viewRouter.currentViewName === "events" &&
				mainElement.scrollHeight <= mainElement.clientHeight + INFINITE_SCROLL_PRELOAD_MARGIN_PX
			) {
				eventModule.loadNextPage().then(function () {
					eventModule.loadNextPageIfViewportNotFilled();
				});
			}
		},

		/** 建立無限捲動的觀察器 */
		initialiseInfiniteScroll: function () {
			const mainElement = byId("appMain");
			const sentinelElement = byId("eventSentinel");

			eventModule.sentinelObserver = new IntersectionObserver(
				function (entries) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting && viewRouter.currentViewName === "events") {
							eventModule.loadNextPage();
						}
					});
				},
				{
					root: mainElement,
					/* 提前 500px 開始預先載入，捲動時感覺不到等待 */
					rootMargin: INFINITE_SCROLL_PRELOAD_MARGIN_PX + "px 0px",
					threshold: 0
				}
			);
			eventModule.sentinelObserver.observe(sentinelElement);
		},

		/** 初始化 */
		initialise: function () {
			const filterForm = byId("eventFilterForm");

			/* 任一日期輸入改變即重新查詢 */
			filterForm.addEventListener("change", function () {
				eventModule.reloadWithCurrentFilter();
			});

			/* 清除日期後回到預設查詢結果 */
			filterForm.addEventListener("reset", function () {
				window.setTimeout(function () {
					eventModule.reloadWithCurrentFilter();
				}, 0);
			});

			eventModule.initialiseInfiniteScroll();
			eventModule.reloadWithCurrentFilter();
		}
	};

	/* ---------------------------------------------------------------------
	 * weatherModule：一週天氣、SVG 圖示
	 * ------------------------------------------------------------------- */
	const weatherModule = {
		/**
		 * 各天氣狀態對應的 SVG 內容。
		 * 所有圖形皆為 fill:none / stroke:#1c3e60 / stroke-width:1（樣式定義於 index.css）。
		 */
		iconShapes: {
			sunny:
				'<circle cx="32" cy="32" r="11"></circle>' +
				'<path d="M32 8v8M32 48v8M8 32h8M48 32h8M15 15l6 6M43 43l6 6M49 15l-6 6M21 43l-6 6"></path>',
			"partly-cloudy":
				'<circle cx="24" cy="22" r="8"></circle>' +
				'<path d="M24 6v5M8 22h5M12 10l3.5 3.5M36 10l-3.5 3.5"></path>' +
				'<path d="M24 50h20a8 8 0 0 0 0-16 11 11 0 0 0-20.8 3.2A6.5 6.5 0 0 0 24 50z"></path>',
			cloudy:
				'<path d="M20 46h24a9 9 0 0 0 0-18 13 13 0 0 0-24.6 3.8A7.5 7.5 0 0 0 20 46z"></path>' +
				'<path d="M14 34a7 7 0 0 1 3-13"></path>',
			rain:
				'<path d="M20 38h24a9 9 0 0 0 0-18 13 13 0 0 0-24.6 3.8A7.5 7.5 0 0 0 20 38z"></path>' +
				'<path d="M22 44l-3 8M32 44l-3 8M42 44l-3 8"></path>',
			thunderstorm:
				'<path d="M20 36h24a9 9 0 0 0 0-18 13 13 0 0 0-24.6 3.8A7.5 7.5 0 0 0 20 36z"></path>' +
				'<polyline points="32 40 26 40 32 50 24 50 30 58"></polyline>',
			snow:
				'<path d="M20 36h24a9 9 0 0 0 0-18 13 13 0 0 0-24.6 3.8A7.5 7.5 0 0 0 20 36z"></path>' +
				'<path d="M24 44v8M20 46l8 4M28 46l-8 4M42 44v8M38 46l8 4M46 46l-8 4"></path>',
			fog:
				'<path d="M20 34h24a9 9 0 0 0 0-18 13 13 0 0 0-24.6 3.8A7.5 7.5 0 0 0 20 34z"></path>' +
				'<path d="M16 42h32M20 48h24M24 54h16"></path>'
		},

		/**
		 * 依 icon 代號建立 SVG 元素。
		 * @param {string} iconName API 回傳的 icon 欄位
		 * @returns {SVGElement}
		 */
		buildIconElement: function (iconName) {
			const svgElement = document.createElementNS("http://www.w3.org/2000/svg", "svg");
			svgElement.setAttribute("viewBox", "0 0 64 64");
			svgElement.setAttribute("class", "weather-icon");
			svgElement.setAttribute("aria-hidden", "true");
			svgElement.setAttribute("focusable", "false");
			/* 內容為程式內建的靜態字串，不含 API 文字，無 XSS 風險 */
			svgElement.innerHTML = weatherModule.iconShapes[iconName] || weatherModule.iconShapes.cloudy;
			return svgElement;
		},

		/** 建立單日天氣卡片 */
		buildDayCard: function (dayForecast) {
			const dayCard = createElement("article", {
				className: "weather-day",
				attributes: { tabindex: "0" }
			});

			dayCard.appendChild(createElement("h3", { className: "weather-weekday", textContent: dayForecast.weekday }));
			dayCard.appendChild(createElement("p", { className: "weather-date", textContent: dayForecast.date }));
			dayCard.appendChild(weatherModule.buildIconElement(dayForecast.icon));
			dayCard.appendChild(
				createElement("p", { className: "weather-condition", textContent: dayForecast.condition })
			);

			const temperatureLine = createElement("p", {
				className: "weather-temperature",
				textContent: dayForecast.temperature_high + "°C "
			});
			temperatureLine.appendChild(
				createElement("span", {
					className: "weather-temperature-low",
					textContent: "/ " + dayForecast.temperature_low + "°C"
				})
			);
			dayCard.appendChild(temperatureLine);

			dayCard.appendChild(
				createElement("p", {
					className: "weather-extra",
					textContent:
						"Humidity " + dayForecast.humidity + "% · Wind " + dayForecast.wind_speed + " km/h"
				})
			);

			return dayCard;
		},

		/** 從 API 載入並繪製一週天氣 */
		loadWeather: async function () {
			const scroller = byId("weatherScroller");
			try {
				const responseData = await apiClient.getWeather();
				const dailyForecast = Array.isArray(responseData.daily) ? responseData.daily : [];

				scroller.textContent = "";
				const fragment = document.createDocumentFragment();
				dailyForecast.forEach(function (dayForecast) {
					fragment.appendChild(weatherModule.buildDayCard(dayForecast));
				});
				scroller.appendChild(fragment);

				byId("weatherViewHeading").textContent = responseData.city + " — next 7 days";
			} catch (error) {
				scroller.textContent = "Unable to load weather data.";
			}
		},

		/** 初始化（首次進入天氣檢視時才載入） */
		initialise: function () {
			weatherModule.loadWeather();
		}
	};

	/* ---------------------------------------------------------------------
	 * viewRouter：檢視切換與頁首
	 * ------------------------------------------------------------------- */
	const viewRouter = {
		/** 檢視名稱 → { sectionId, title, showBackButton } */
		viewDefinitions: {
			carparks: { sectionId: "viewCarparks", title: "Carparks", showBackButton: false },
			carparkFocus: { sectionId: "viewCarparkFocus", title: "Carpark", showBackButton: true },
			events: { sectionId: "viewEvents", title: "Events", showBackButton: false },
			weather: { sectionId: "viewWeather", title: "Weather", showBackButton: false },
			setting: { sectionId: "viewSetting", title: "Setting", showBackButton: false }
		},

		/** 目前顯示中的檢視名稱 */
		currentViewName: "carparks",

		/**
		 * 設定頁首標題。
		 * @param {string} title 標題文字
		 */
		setHeaderTitle: function (title) {
			byId("headerTitle").textContent = title;
		},

		/**
		 * 切換檢視。
		 * @param {string} viewName 檢視名稱
		 */
		showView: function (viewName) {
			const viewDefinition = viewRouter.viewDefinitions[viewName];
			if (!viewDefinition) {
				return;
			}

			/* 離開單一停車場檢視時停掉自動更新 */
			if (viewRouter.currentViewName === "carparkFocus" && viewName !== "carparkFocus") {
				carparkModule.stopFocusRefreshTimer();
			}

			viewRouter.currentViewName = viewName;

			Object.keys(viewRouter.viewDefinitions).forEach(function (name) {
				const sectionElement = byId(viewRouter.viewDefinitions[name].sectionId);
				sectionElement.hidden = name !== viewName;
			});

			viewRouter.setHeaderTitle(viewDefinition.title);
			byId("headerBackButton").hidden = !viewDefinition.showBackButton;

			/* 底部導覽列的目前位置標示（單一停車場檢視仍屬於 Carparks） */
			const activeNavView = viewName === "carparkFocus" ? "carparks" : viewName;
			document.querySelectorAll(".nav-button").forEach(function (navButton) {
				if (navButton.dataset.view === activeNavView) {
					navButton.setAttribute("aria-current", "page");
				} else {
					navButton.removeAttribute("aria-current");
				}
			});

			/* 每次切換檢視都把內容捲回頂端 */
			byId("appMain").scrollTop = 0;

			/* 進入活動檢視時，確認首屏已填滿資料 */
			if (viewName === "events") {
				eventModule.loadNextPageIfViewportNotFilled();
			}
			/* 再次進入停車場檢視時重新取得最新空位（首次載入由 carparkModule 自行處理） */
			if (viewName === "carparks" && carparkModule.carparks.length > 0) {
				carparkModule.loadCarparks(true);
			}
		},

		/** 綁定底部導覽列事件 */
		initialise: function () {
			document.querySelectorAll(".nav-button").forEach(function (navButton) {
				navButton.addEventListener("click", function () {
					viewRouter.showView(navButton.dataset.view);
				});
			});
			viewRouter.showView("carparks");
		}
	};

	/* ---------------------------------------------------------------------
	 * 應用程式啟動
	 * ------------------------------------------------------------------- */
	function startApplication() {
		settingModule.initialise();
		geolocationModule.initialise();
		viewRouter.initialise();
		carparkModule.initialise();
		eventModule.initialise();
		weatherModule.initialise();
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", startApplication);
	} else {
		startApplication();
	}
})();
