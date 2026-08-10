/*
	幻燈片播放引擎：建立投影片、切換主題、三種播放模式、全螢幕
*/

/** 自動 / 隨機播放時，每張照片停留的毫秒數 */
const SLIDE_INTERVAL_MS = 4000;

/** 離場動畫的長度，時間到才把離場的投影片移出 DOM */
const SLIDE_LEAVE_MS = 1200;

/** 主題 D 會把照片疊起來，最多保留的堆疊張數 */
const STACK_KEEP_COUNT = 6;

/** 播放模式對應的中文名稱，顯示在狀態列 */
const MODE_LABELS = {
	manual: "手動控制",
	auto: "自動播放",
	random: "隨機播放"
};

const Slideshow = {
	stageElement: null,
	statusElement: null,

	photos: [],
	currentIndex: -1,
	mode: "manual",
	theme: "A",
	timerId: null,

	/** 初始化：記住需要操作的元素並綁定鍵盤控制 */
	init: function(stageElement, statusElement){
		this.stageElement = stageElement;
		this.statusElement = statusElement;

		this.bindKeyboard();
		this.bindFullscreenChange();
		this.applyThemeClass();
		this.updateStatus();
	},

	/** 鍵盤控制：左右方向鍵切換照片，F 鍵切換全螢幕 */
	bindKeyboard: function(){
		const slideshow = this;

		document.addEventListener("keydown", function(event){
			// 命令欄開啟時，鍵盤事件交給命令欄處理
			if(CommandBar.isOpen){
				return;
			}

			// 在輸入欄位中打字時不攔截
			const tagName = event.target && event.target.tagName ? event.target.tagName.toLowerCase() : "";

			if(tagName === "input" || tagName === "textarea" || tagName === "select"){
				return;
			}

			if(event.key === "ArrowLeft"){
				event.preventDefault();
				slideshow.showPrevious();
			}else if(event.key === "ArrowRight"){
				event.preventDefault();
				slideshow.showNext();
			}else if(event.key === "f" || event.key === "F"){
				slideshow.toggleFullscreen();
			}
		});
	},

	/** 更新照片來源（載入新照片或調整排序後呼叫） */
	setPhotos: function(photos){
		this.photos = photos;
		this.clearStage();

		if(photos.length > 0){
			this.currentIndex = 0;
			this.renderSlide(0);
		}else{
			this.currentIndex = -1;
			this.stageElement.classList.remove("has-photo");
		}

		this.restartTimer();
		this.updateStatus();
	},

	/** 切換播放模式：manual / auto / random */
	setMode: function(mode){
		this.mode = mode;
		this.restartTimer();
		this.updateStatus();
	},

	/** 切換主題 A ~ F，並重新播放目前這張照片以套用新動畫 */
	setTheme: function(theme){
		this.theme = theme;
		this.applyThemeClass();

		if(this.currentIndex >= 0){
			this.clearStage();
			this.renderSlide(this.currentIndex);
		}

		this.updateStatus();
	},

	/** 把主題 class 套到舞台上（例如 theme-b） */
	applyThemeClass: function(){
		this.stageElement.className = "stage theme-" + this.theme.toLowerCase();

		if(this.photos.length > 0){
			this.stageElement.classList.add("has-photo");
		}
	},

	/** 清空舞台上的所有投影片 */
	clearStage: function(){
		selectElementAll(".slide", this.stageElement).forEach(function(slideElement){
			slideElement.remove();
		});
	},

	/** 建立一張投影片的 DOM 結構 */
	createSlideElement: function(photo){
		const slideElement = document.createElement("figure");

		slideElement.className = "slide";
		// 主題 E 的門片需要照片網址；主題 D 需要隨機旋轉角度
		slideElement.style.setProperty("--photo-url", "url(\"" + photo.url + "\")");
		slideElement.style.setProperty("--rotation", randomRotationDegree() + "deg");

		const cardElement = document.createElement("div");

		cardElement.className = "card";

		const frameElement = document.createElement("div");

		frameElement.className = "frame";

		const imageElement = document.createElement("img");

		imageElement.className = "photo";
		imageElement.src = photo.url;
		imageElement.alt = photo.caption;

		// 主題 E 用的左右兩片門，其他主題以 CSS 隱藏
		const leftDoorElement = document.createElement("span");

		leftDoorElement.className = "door door-left";
		leftDoorElement.setAttribute("aria-hidden", "true");

		const rightDoorElement = document.createElement("span");

		rightDoorElement.className = "door door-right";
		rightDoorElement.setAttribute("aria-hidden", "true");

		frameElement.appendChild(imageElement);
		frameElement.appendChild(leftDoorElement);
		frameElement.appendChild(rightDoorElement);

		cardElement.appendChild(frameElement);
		cardElement.appendChild(buildCaptionElement(photo.caption));
		slideElement.appendChild(cardElement);

		return slideElement;
	},

	/** 顯示第 index 張照片 */
	renderSlide: function(index){
		const photo = this.photos[index];

		if(!photo){
			return;
		}

		this.currentIndex = index;
		this.stageElement.classList.add("has-photo");

		// 目前這張改為離場狀態
		const leavingElement = selectElement(".slide.is-active", this.stageElement);

		if(leavingElement){
			leavingElement.classList.remove("is-active");
			leavingElement.classList.add("is-leaving");
			this.scheduleLeavingCleanup(leavingElement);
		}

		const slideElement = this.createSlideElement(photo);

		this.stageElement.appendChild(slideElement);

		// 強制瀏覽器重算版面，確保 is-active 的動畫一定會從頭播放
		void slideElement.offsetWidth;
		slideElement.classList.add("is-active");

		this.updateStatus();
	},

	/**
	 * 處理離場中的投影片
	 * 主題 D 需要保留照片形成堆疊，因此只在超過張數上限時才移除最舊的
	 */
	scheduleLeavingCleanup: function(leavingElement){
		if(this.theme === "D"){
			const leavingElements = selectElementAll(".slide.is-leaving", this.stageElement);

			leavingElements.slice(0, Math.max(0, leavingElements.length - STACK_KEEP_COUNT)).forEach(function(oldElement){
				oldElement.remove();
			});

			return;
		}

		window.setTimeout(function(){
			leavingElement.remove();
		}, SLIDE_LEAVE_MS);
	},

	/** 下一張（播到最後一張會回到第一張） */
	showNext: function(){
		if(this.photos.length === 0){
			return;
		}

		this.renderSlide((this.currentIndex + 1) % this.photos.length);
	},

	/** 上一張 */
	showPrevious: function(){
		if(this.photos.length === 0){
			return;
		}

		this.renderSlide((this.currentIndex - 1 + this.photos.length) % this.photos.length);
	},

	/** 隨機一張（照片超過一張時不會重複顯示目前這張） */
	showRandom: function(){
		if(this.photos.length === 0){
			return;
		}

		let randomIndex = Math.floor(Math.random() * this.photos.length);

		if(this.photos.length > 1){
			while(randomIndex === this.currentIndex){
				randomIndex = Math.floor(Math.random() * this.photos.length);
			}
		}

		this.renderSlide(randomIndex);
	},

	/** 依照目前模式重新啟動計時器（手動模式不需要計時器） */
	restartTimer: function(){
		const slideshow = this;

		if(this.timerId !== null){
			window.clearInterval(this.timerId);
			this.timerId = null;
		}

		if(this.photos.length === 0 || this.mode === "manual"){
			return;
		}

		this.timerId = window.setInterval(function(){
			if(slideshow.mode === "auto"){
				slideshow.showNext();
			}else{
				slideshow.showRandom();
			}
		}, SLIDE_INTERVAL_MS);
	},

	/**
	 * 切換全螢幕瀏覽（全螢幕時瀏覽器工具列與工作列都會隱藏）
	 * 對整份文件要求全螢幕，設定面板與命令欄在全螢幕下仍可使用
	 */
	toggleFullscreen: function(){
		if(document.fullscreenElement){
			document.exitFullscreen();
		}else if(document.documentElement.requestFullscreen){
			document.documentElement.requestFullscreen();
		}
	},

	/** 進出全螢幕時切換版面（隱藏工具列、拖放區與控制列） */
	bindFullscreenChange: function(){
		document.addEventListener("fullscreenchange", function(){
			document.body.classList.toggle("is-fullscreen", Boolean(document.fullscreenElement));
		});
	},

	/** 更新狀態列文字 */
	updateStatus: function(){
		if(!this.statusElement){
			return;
		}

		if(this.photos.length === 0){
			this.statusElement.textContent = "尚未載入照片";

			return;
		}

		const photo = this.photos[this.currentIndex];

		this.statusElement.textContent =
			(this.currentIndex + 1) + " / " + this.photos.length +
			" ・ " + MODE_LABELS[this.mode] +
			" ・ 主題 " + this.theme +
			(photo ? " ・ " + photo.caption : "");
	}
};
