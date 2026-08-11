/**
 * GameStorage
 * 以 localStorage 保存 / 還原遊戲狀態。
 * 保存內容：設定變數（名稱、難度、切片數）、裁切後的圖片、
 *           每個碎片的位置與旋轉、已完成的碎片、以及經過時間。
 */
class GameStorage {

	/**
	 * @param {string} [key] localStorage 的鍵名
	 */
	constructor(key = 'wsc2015.customPuzzle.state') {
		this.key = key;
	}

	/** localStorage 是否可用 */
	get available() {
		try {
			const probe = '__probe__';
			window.localStorage.setItem(probe, '1');
			window.localStorage.removeItem(probe);
			return true;
		} catch (e) {
			return false;
		}
	}

	/**
	 * 寫入狀態。
	 * @param {Object} state
	 */
	save(state) {
		if (!this.available) {
			return false;
		}
		try {
			window.localStorage.setItem(this.key, JSON.stringify(state));
			return true;
		} catch (e) {
			// 例如超出配額時，不讓遊戲中斷
			console.warn('[CustomPuzzle] Unable to persist the game state:', e.message);
			return false;
		}
	}

	/**
	 * 讀取狀態。
	 * @returns {Object|null}
	 */
	load() {
		if (!this.available) {
			return null;
		}
		try {
			const raw = window.localStorage.getItem(this.key);
			if (!raw) {
				return null;
			}
			const data = JSON.parse(raw);
			return (data && data.image && data.pieces) ? data : null;
		} catch (e) {
			return null;
		}
	}

	/** 清除已保存的狀態 */
	clear() {
		if (this.available) {
			window.localStorage.removeItem(this.key);
		}
	}
}

window.GameStorage = GameStorage;
