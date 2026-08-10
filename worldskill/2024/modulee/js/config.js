/*
	設定面板：播放模式切換、主題切換、照片拖放排序
*/

const ConfigPanel = {
	panelElement: null,
	orderListElement: null,

	/** 拖曳排序時，被拖曳項目的索引 */
	draggingIndex: -1,

	/** 模式或主題被改變時要通知外部（由 index.js 指定） */
	onModeChange: function(){},
	onThemeChange: function(){},

	init: function(options){
		this.panelElement = selectElement("#config-panel");
		this.orderListElement = selectElement("#order-list");
		this.onModeChange = options.onModeChange;
		this.onThemeChange = options.onThemeChange;

		this.bindToggleButtons();
		this.bindModeInputs();
		this.bindThemeInputs();
	},

	/** 開關設定面板的按鈕 */
	bindToggleButtons: function(){
		const configPanel = this;

		selectElement("#config-button").addEventListener("click", function(){
			configPanel.toggle();
		});

		selectElement("#config-close-button").addEventListener("click", function(){
			configPanel.close();
		});
	},

	open: function(){
		this.panelElement.classList.add("is-open");
	},

	close: function(){
		this.panelElement.classList.remove("is-open");
	},

	toggle: function(){
		this.panelElement.classList.toggle("is-open");
	},

	/** 播放模式的三個選項 */
	bindModeInputs: function(){
		const configPanel = this;

		selectElementAll("input[name='operating-mode']").forEach(function(inputElement){
			inputElement.addEventListener("change", function(){
				if(inputElement.checked){
					configPanel.onModeChange(inputElement.value);
				}
			});
		});
	},

	/** 主題 A ~ F 的選項 */
	bindThemeInputs: function(){
		const configPanel = this;

		selectElementAll("input[name='theme']").forEach(function(inputElement){
			inputElement.addEventListener("change", function(){
				if(inputElement.checked){
					configPanel.onThemeChange(inputElement.value);
				}
			});
		});
	},

	/** 讓面板上的選項反映目前設定 */
	syncSettings: function(settings){
		const modeInput = selectElement("input[name='operating-mode'][value='" + settings.mode + "']");
		const themeInput = selectElement("input[name='theme'][value='" + settings.theme + "']");

		if(modeInput){
			modeInput.checked = true;
		}

		if(themeInput){
			themeInput.checked = true;
		}
	},

	/**
	 * 重繪照片排序清單
	 * 這份清單同時也是「停用 CSS 時」網頁上列出所有已載入照片的地方
	 */
	renderOrderList: function(photos){
		const configPanel = this;

		this.orderListElement.innerHTML = "";

		photos.forEach(function(photo, photoIndex){
			const itemElement = document.createElement("li");

			itemElement.className = "order-item";
			itemElement.draggable = true;
			itemElement.dataset.index = String(photoIndex);

			const indexElement = document.createElement("span");

			indexElement.className = "order-item-index";
			indexElement.textContent = String(photoIndex + 1);

			const thumbElement = document.createElement("img");

			thumbElement.className = "order-item-thumb";
			thumbElement.src = photo.url;
			thumbElement.alt = photo.caption;

			const captionElement = document.createElement("span");

			captionElement.className = "order-item-caption";
			captionElement.textContent = photo.caption;

			itemElement.appendChild(indexElement);
			itemElement.appendChild(thumbElement);
			itemElement.appendChild(captionElement);
			configPanel.bindOrderItemDragEvents(itemElement);
			configPanel.orderListElement.appendChild(itemElement);
		});
	},

	/** 綁定單一清單項目的拖放排序事件 */
	bindOrderItemDragEvents: function(itemElement){
		const configPanel = this;

		itemElement.addEventListener("dragstart", function(event){
			configPanel.draggingIndex = Number(itemElement.dataset.index);
			itemElement.classList.add("is-dragging");
			event.dataTransfer.effectAllowed = "move";
			// 部分瀏覽器必須設定資料，拖曳才會生效
			event.dataTransfer.setData("text/plain", itemElement.dataset.index);
		});

		itemElement.addEventListener("dragover", function(event){
			// 只處理清單內部的排序，拖入檔案時不攔截
			if(configPanel.draggingIndex < 0){
				return;
			}

			event.preventDefault();
			event.dataTransfer.dropEffect = "move";
			itemElement.classList.add("is-drop-target");
		});

		itemElement.addEventListener("dragleave", function(){
			itemElement.classList.remove("is-drop-target");
		});

		itemElement.addEventListener("drop", function(event){
			if(configPanel.draggingIndex < 0){
				return;
			}

			event.preventDefault();
			event.stopPropagation();
			itemElement.classList.remove("is-drop-target");

			const targetIndex = Number(itemElement.dataset.index);

			PhotoStore.movePhoto(configPanel.draggingIndex, targetIndex);
			configPanel.draggingIndex = -1;
		});

		itemElement.addEventListener("dragend", function(){
			itemElement.classList.remove("is-dragging");
			configPanel.draggingIndex = -1;
		});
	}
};
