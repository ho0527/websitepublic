/**
 * GameTimer
 * 計時器：可啟動、暫停、繼續、停止，並以 mm:ss 呈現經過時間。
 */
class GameTimer {

	/**
	 * @param {HTMLElement} displayEl 顯示時間的元素
	 * @param {Function}    [onTick]  每次更新時的回呼（傳入毫秒數）
	 */
	constructor(displayEl, onTick) {
		this.displayEl = displayEl;
		this.onTick = onTick || null;
		this.elapsed = 0;        // 已累積的毫秒數
		this.startedAt = 0;      // 本段計時的起始時間戳
		this.running = false;
		this.intervalId = null;
		this.render();
	}

	/** 目前累計的毫秒數（含正在進行中的這一段） */
	get value() {
		return this.running ? this.elapsed + (Date.now() - this.startedAt) : this.elapsed;
	}

	/** 目前累計的秒數（整數，寫入資料庫用） */
	get seconds() {
		return Math.round(this.value / 1000);
	}

	/**
	 * 從指定毫秒數開始計時（重新載入時可帶入先前的進度）。
	 * @param {number} [fromMs=0]
	 */
	start(fromMs = 0) {
		this.elapsed = fromMs;
		this.startedAt = Date.now();
		this.running = true;
		this.tick();
		this.intervalId = window.setInterval(() => this.tick(), 250);
	}

	/** 暫停計時 */
	pause() {
		if (!this.running) {
			return;
		}
		this.elapsed += Date.now() - this.startedAt;
		this.running = false;
		window.clearInterval(this.intervalId);
		this.intervalId = null;
		this.render();
	}

	/** 從暫停狀態繼續 */
	resume() {
		if (this.running) {
			return;
		}
		this.startedAt = Date.now();
		this.running = true;
		this.intervalId = window.setInterval(() => this.tick(), 250);
	}

	/** 停止並保留最終時間（完成拼圖時使用） */
	stop() {
		this.pause();
	}

	/** 歸零 */
	reset() {
		this.pause();
		this.elapsed = 0;
		this.render();
	}

	/** 內部：更新顯示並觸發回呼 */
	tick() {
		this.render();
		if (this.onTick) {
			this.onTick(this.value);
		}
	}

	/** 依目前時間更新畫面 */
	render() {
		if (this.displayEl) {
			this.displayEl.textContent = GameTimer.format(this.value);
		}
	}

	/**
	 * 毫秒轉 mm:ss。
	 * @param {number} ms
	 * @returns {string}
	 */
	static format(ms) {
		const total = Math.floor(ms / 1000);
		const minutes = Math.floor(total / 60);
		const seconds = total % 60;
		return String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
	}
}

window.GameTimer = GameTimer;
