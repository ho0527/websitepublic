/*
	共用工具函式：DOM 選取、檔名轉標題、設定的儲存與讀取
*/

/** localStorage 的 key 前綴，避免與同網域其他專案衝突 */
const STORAGE_KEY = "worldskill2024modulee-settings";

/** 取得第一個符合選擇器的元素 */
function selectElement(selector, root = document){
	return root.querySelector(selector);
}

/** 取得所有符合選擇器的元素（回傳陣列，方便使用 map/forEach） */
function selectElementAll(selector, root = document){
	return Array.prototype.slice.call(root.querySelectorAll(selector));
}

/**
 * 由檔名產生照片標題
 * 規則：去掉副檔名、把連字號與底線換成空白、每個單字首字母大寫
 * 例："a-sample-photo.jpg" -> "A Sample Photo"
 */
function captionFromFileName(fileName){
	const nameWithoutExtension = String(fileName).replace(/\.[^./\\]+$/, "");
	const words = nameWithoutExtension
		.replace(/[-_]+/g, " ")
		.split(/\s+/)
		.filter(function(word){
			return word.length > 0;
		});

	return words.map(function(word){
		return word.charAt(0).toUpperCase() + word.slice(1);
	}).join(" ");
}

/** 把標題拆成單字，供主題 C、F 做逐字動畫 */
function buildCaptionElement(captionText){
	const captionElement = document.createElement("figcaption");
	captionElement.className = "caption";

	captionText.split(" ").forEach(function(word, wordIndex){
		const wordElement = document.createElement("span");
		wordElement.className = "word";
		wordElement.style.setProperty("--word-index", String(wordIndex));
		wordElement.textContent = word;
		captionElement.appendChild(wordElement);

		// 單字之間補回空白，避免所有字黏在一起
		captionElement.appendChild(document.createTextNode(" "));
	});

	return captionElement;
}

/** 讀取設定（讀不到或格式錯誤時回傳預設值） */
function loadSettings(defaultSettings){
	try{
		const savedText = window.localStorage.getItem(STORAGE_KEY);

		if(!savedText){
			return Object.assign({}, defaultSettings);
		}

		return Object.assign({}, defaultSettings, JSON.parse(savedText));
	}catch(error){
		return Object.assign({}, defaultSettings);
	}
}

/** 儲存設定 */
function saveSettings(settings){
	try{
		window.localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
	}catch(error){
		// 使用者可能停用 localStorage，忽略即可，不影響主要功能
	}
}

/** 產生 -5 ~ 5 度之間的隨機旋轉角度（主題 D 使用） */
function randomRotationDegree(){
	return (Math.random() * 10 - 5).toFixed(2);
}
