/*
	命令欄：Ctrl+K 或 "/" 開啟，ESC 關閉
	以上下鍵選擇命令、Enter 執行，輸入文字可過濾出部分符合的命令
*/

const CommandBar = {
	isOpen: false,

	barElement: null,
	inputElement: null,
	optionsElement: null,

	/** 全部命令，於 init 時由 index.js 提供實際執行的動作 */
	commands: [],

	/** 目前過濾後顯示的命令 */
	visibleCommands: [],

	/** 目前選取的命令在 visibleCommands 中的索引 */
	selectedIndex: 0,

	init: function(commands){
		this.barElement = selectElement("#command-bar");
		this.inputElement = selectElement("#command-input");
		this.optionsElement = selectElement("#command-options");
		this.commands = commands;

		this.bindGlobalShortcut();
		this.bindDialogEvents();
	},

	/** 全域快速鍵：Ctrl+K 或 "/" 開啟命令欄 */
	bindGlobalShortcut: function(){
		const commandBar = this;

		document.addEventListener("keydown", function(event){
			if(commandBar.isOpen){
				return;
			}

			const tagName = event.target && event.target.tagName ? event.target.tagName.toLowerCase() : "";
			const isTyping = tagName === "input" || tagName === "textarea" || tagName === "select";

			if((event.ctrlKey || event.metaKey) && (event.key === "k" || event.key === "K")){
				event.preventDefault();
				commandBar.open();
			}else if(event.key === "/" && !isTyping){
				event.preventDefault();
				commandBar.open();
			}
		});

		selectElement("#command-button").addEventListener("click", function(){
			commandBar.open();
		});
	},

	/** 命令欄自身的鍵盤與滑鼠操作 */
	bindDialogEvents: function(){
		const commandBar = this;

		this.inputElement.addEventListener("input", function(){
			commandBar.selectedIndex = 0;
			commandBar.renderOptions();
		});

		this.inputElement.addEventListener("keydown", function(event){
			if(event.key === "ArrowDown"){
				event.preventDefault();
				commandBar.moveSelection(1);
			}else if(event.key === "ArrowUp"){
				event.preventDefault();
				commandBar.moveSelection(-1);
			}else if(event.key === "Enter"){
				event.preventDefault();
				commandBar.executeSelected();
			}else if(event.key === "Escape"){
				event.preventDefault();
				commandBar.close();
			}
		});

		// 點擊命令欄以外的暗色區域也可關閉
		this.barElement.addEventListener("click", function(event){
			if(event.target === commandBar.barElement){
				commandBar.close();
			}
		});
	},

	open: function(){
		this.isOpen = true;
		this.barElement.classList.add("is-open");
		this.inputElement.value = "";
		this.selectedIndex = 0;
		this.renderOptions();
		this.inputElement.focus();
	},

	close: function(){
		this.isOpen = false;
		this.barElement.classList.remove("is-open");
		this.inputElement.blur();
	},

	/** 依輸入文字過濾命令（不分大小寫的部分比對） */
	filterCommands: function(){
		const keyword = this.inputElement.value.trim().toLowerCase();

		if(keyword === ""){
			return this.commands.slice();
		}

		// 以空白拆成多個關鍵字，每個關鍵字都要是命令中某個單字的開頭才算符合
		// （用單字開頭比對，輸入 "theme e" 才不會把 A ~ F 全部列出來）
		const keywordList = keyword.split(/\s+/);

		return this.commands.filter(function(command){
			const commandWords = (command.label + " " + command.keywords).toLowerCase().split(/[\s-]+/);

			return keywordList.every(function(singleKeyword){
				return commandWords.some(function(commandWord){
					return commandWord.indexOf(singleKeyword) === 0;
				});
			});
		});
	},

	/** 重新繪製命令選項清單 */
	renderOptions: function(){
		const commandBar = this;

		this.visibleCommands = this.filterCommands();
		this.optionsElement.innerHTML = "";

		if(this.visibleCommands.length === 0){
			const emptyElement = document.createElement("li");

			emptyElement.className = "command-bar-empty";
			emptyElement.textContent = "找不到符合的命令";
			this.optionsElement.appendChild(emptyElement);

			return;
		}

		if(this.selectedIndex >= this.visibleCommands.length){
			this.selectedIndex = this.visibleCommands.length - 1;
		}

		this.visibleCommands.forEach(function(command, commandIndex){
			const optionElement = document.createElement("li");

			optionElement.className = "command-bar-option";
			optionElement.setAttribute("role", "option");

			if(commandIndex === commandBar.selectedIndex){
				optionElement.classList.add("is-selected");
				optionElement.setAttribute("aria-selected", "true");
			}

			const labelElement = document.createElement("span");

			labelElement.textContent = command.label;

			const keyElement = document.createElement("span");

			keyElement.className = "command-bar-option-key";
			keyElement.textContent = command.group;

			optionElement.appendChild(labelElement);
			optionElement.appendChild(keyElement);

			// 滑鼠也可以直接點選命令
			optionElement.addEventListener("mousedown", function(event){
				event.preventDefault();
				commandBar.selectedIndex = commandIndex;
				commandBar.executeSelected();
			});

			commandBar.optionsElement.appendChild(optionElement);
		});
	},

	/** 以上下鍵移動選取項目（頭尾循環） */
	moveSelection: function(step){
		if(this.visibleCommands.length === 0){
			return;
		}

		this.selectedIndex = (this.selectedIndex + step + this.visibleCommands.length) % this.visibleCommands.length;
		this.renderOptions();
	},

	/** 執行目前選取的命令 */
	executeSelected: function(){
		const command = this.visibleCommands[this.selectedIndex];

		if(!command){
			return;
		}

		command.run();
		this.close();
	}
};
