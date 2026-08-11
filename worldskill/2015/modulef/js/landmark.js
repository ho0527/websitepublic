/**
 * Landmark
 * 一個地標（圖片 + 路邊招牌）。一開始隱藏，跑者經過招牌時以動畫呈現。
 * 每個地標使用不同的動畫（定義在 main.css 中，以 id 區分）。
 */
class Landmark {

	/**
	 * @param {Object} definition GameConfig.landmarks 的其中一項
	 * @param {number} index      出現順序（0 起算）
	 */
	constructor(definition, index) {
		this.index = index;
		this.name = definition.name;
		this.sectionEl = document.getElementById(definition.key);
		this.panelEl = document.getElementById(definition.panel);
		this.triggerX = GameConfig.landmarkTriggerX(index);
		this.revealed = false;
	}

	/** 回到隱藏狀態 */
	reset() {
		this.revealed = false;
		if (this.sectionEl) {
			this.sectionEl.classList.remove('revealed');
		}
		if (this.panelEl) {
			this.panelEl.classList.remove('revealed');
		}
	}

	/**
	 * 依跑者位置決定是否要呈現。
	 * @param {number} runnerX
	 * @returns {boolean} 這一幀是否剛被觸發
	 */
	update(runnerX) {
		if (this.revealed || runnerX < this.triggerX) {
			return false;
		}
		this.reveal();
		return true;
	}

	/** 播放呈現動畫（圖片與招牌各自有不同的動畫） */
	reveal() {
		this.revealed = true;
		if (this.sectionEl) {
			this.sectionEl.classList.add('revealed');
		}
		if (this.panelEl) {
			// 招牌稍晚一點出現，讓兩段動畫有層次
			window.setTimeout(() => this.panelEl.classList.add('revealed'), 160);
		}
	}
}

window.Landmark = Landmark;
