/*
	主程式：把照片來源、播放引擎、設定面板、命令欄組合起來
*/

/** 預設設定 */
const DEFAULT_SETTINGS = {
	mode: "manual",
	theme: "A"
};

/** 目前設定（會存進 localStorage） */
const settings = loadSettings(DEFAULT_SETTINGS);

/** 套用播放模式並記住設定 */
function applyMode(mode){
	settings.mode = mode;
	saveSettings(settings);
	ConfigPanel.syncSettings(settings);
	Slideshow.setMode(mode);
}

/** 套用主題並記住設定 */
function applyTheme(theme){
	settings.theme = theme;
	saveSettings(settings);
	ConfigPanel.syncSettings(settings);
	Slideshow.setTheme(theme);
}

/** 建立命令欄要用的命令清單（3 種播放模式 + 6 個主題） */
function buildCommands(){
	const commands = [
		{
			label: "Change to manual control mode",
			keywords: "manual control mode 手動",
			group: "模式",
			run: function(){
				applyMode("manual");
			}
		},
		{
			label: "Change to auto-playing mode",
			keywords: "auto playing mode 自動",
			group: "模式",
			run: function(){
				applyMode("auto");
			}
		},
		{
			label: "Change to random playing mode",
			keywords: "random playing mode 隨機",
			group: "模式",
			run: function(){
				applyMode("random");
			}
		}
	];

	["A", "B", "C", "D", "E", "F"].forEach(function(themeName){
		commands.push({
			label: "Switch to theme " + themeName,
			keywords: "switch theme " + themeName + " 主題",
			group: "主題",
			run: function(){
				applyTheme(themeName);
			}
		});
	});

	return commands;
}

/** 綁定照片載入相關的操作：檔案輸入、拖放、範例照片 */
function bindPhotoLoading(){
	const dropAreaElement = selectElement("#drop-area");
	const stageElement = selectElement("#stage");

	// 檔案輸入（停用 CSS 時仍可使用）
	selectElement("#photo-input").addEventListener("change", function(event){
		PhotoStore.setFiles(event.target.files);
	});

	// 載入題目提供的範例照片
	selectElement("#sample-button").addEventListener("click", function(){
		PhotoStore.loadSamplePhotos();
	});

	// 避免拖放到頁面其他地方時，瀏覽器直接開啟圖片檔
	["dragover", "drop"].forEach(function(eventName){
		document.addEventListener(eventName, function(event){
			event.preventDefault();
		});
	});

	// 拖放區與舞台都可以接收拖入的照片檔案
	[dropAreaElement, stageElement].forEach(function(targetElement){
		targetElement.addEventListener("dragover", function(event){
			event.preventDefault();
			event.dataTransfer.dropEffect = "copy";
			dropAreaElement.classList.add("is-dragover");
		});

		targetElement.addEventListener("dragleave", function(){
			dropAreaElement.classList.remove("is-dragover");
		});

		targetElement.addEventListener("drop", function(event){
			event.preventDefault();
			dropAreaElement.classList.remove("is-dragover");

			if(event.dataTransfer.files && event.dataTransfer.files.length > 0){
				PhotoStore.setFiles(event.dataTransfer.files);
			}
		});
	});
}

/** 綁定舞台的上一張 / 下一張 / 全螢幕按鈕 */
function bindStageControls(){
	selectElement("#prev-button").addEventListener("click", function(){
		Slideshow.showPrevious();
	});

	selectElement("#next-button").addEventListener("click", function(){
		Slideshow.showNext();
	});

	selectElement("#fullscreen-button").addEventListener("click", function(){
		Slideshow.toggleFullscreen();
	});
}

/** 啟動整個應用程式 */
function startApplication(){
	Slideshow.init(
		selectElement("#stage"),
		selectElement("#stage-status")
	);

	ConfigPanel.init({
		onModeChange: applyMode,
		onThemeChange: applyTheme
	});

	CommandBar.init(buildCommands());

	// 照片清單一有變動，就同時更新舞台與排序清單
	PhotoStore.onChange(function(photos){
		Slideshow.setPhotos(photos);
		ConfigPanel.renderOrderList(photos);
	});

	bindPhotoLoading();
	bindStageControls();

	// 套用讀取到的設定
	ConfigPanel.syncSettings(settings);
	Slideshow.setMode(settings.mode);
	Slideshow.setTheme(settings.theme);
}

startApplication();
