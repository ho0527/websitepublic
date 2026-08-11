/**
 * PuzzleApp
 * 整個應用程式的控制器：開始視窗、遊戲畫面、暫停、存檔還原與結果視窗。
 */
class PuzzleApp {

	constructor() {
		/** 難度設定：id 對應名稱與每邊塊數 */
		this.levels = {
			1: { name: 'EASY', grid: 2 },   // 4 片
			2: { name: 'MEDIUM', grid: 3 }, // 9 片
			3: { name: 'HARD', grid: 4 }    // 16 片
		};

		this.dom = {
			startModal: document.getElementById('start'),
			endModal: document.getElementById('end'),
			gameContainer: document.getElementById('gameContainer'),
			form: document.getElementById('startForm'),
			nameInput: document.getElementById('name'),
			difficultSelect: document.getElementById('difficult'),
			drop: document.getElementById('drop'),
			dropText: document.getElementById('dropText'),
			dropPreview: document.getElementById('preview-thumb'),
			fileInput: document.getElementById('fileInput'),
			nameError: document.getElementById('nameError'),
			difficultError: document.getElementById('difficultError'),
			imageError: document.getElementById('imageError'),
			playerName: document.getElementById('playername'),
			clock: document.getElementById('clock'),
			preview: document.getElementById('preview'),
			puzzleContainer: document.getElementById('puzzleContainer'),
			puzzle: document.getElementById('puzzle'),
			destination: document.getElementById('puzzleDestination'),
			pauseButton: document.getElementById('pauseButton'),
			restartButton: document.getElementById('restartButton'),
			restartApplication: document.getElementById('restartApplication'),
			pauseOverlay: document.getElementById('pauseOverlay'),
			rankBody: document.getElementById('rankBody'),
			rankMessage: document.getElementById('rankMessage'),
			endSummary: document.getElementById('endSummary')
		};

		this.storage = new GameStorage();
		this.ranking = new RankingService();
		this.timer = new GameTimer(this.dom.clock, () => this.throttledSave());

		this.puzzle = new Puzzle({
			startEl: this.dom.puzzle,
			destEl: this.dom.destination,
			size: 500,
			onChange: () => this.saveState(),
			onSolved: () => this.handleSolved()
		});

		this.config = null;      // { name, difficultId, grid, image }
		this.paused = false;
		this.lastSaveAt = 0;

		this.bindEvents();
		this.restoreFromStorage();
	}

	/* ------------------------------------------------------------------ */
	/* 事件綁定                                                            */
	/* ------------------------------------------------------------------ */

	bindEvents() {
		this.dom.form.addEventListener('submit', (e) => {
			e.preventDefault();
			this.handleStart();
		});

		// 點擊拖放區可改用檔案選擇（提高可用性）
		this.dom.drop.addEventListener('click', () => this.dom.fileInput.click());
		this.dom.fileInput.addEventListener('change', (e) => {
			if (e.target.files && e.target.files.length) {
				this.handleFile(e.target.files[0]);
			}
		});

		['dragenter', 'dragover'].forEach((type) => {
			this.dom.drop.addEventListener(type, (e) => {
				e.preventDefault();
				e.stopPropagation();
				this.dom.drop.classList.add('dragover');
			});
		});
		['dragleave', 'drop'].forEach((type) => {
			this.dom.drop.addEventListener(type, (e) => {
				e.preventDefault();
				e.stopPropagation();
				this.dom.drop.classList.remove('dragover');
			});
		});
		this.dom.drop.addEventListener('drop', (e) => {
			const files = e.dataTransfer ? e.dataTransfer.files : null;
			if (files && files.length) {
				this.handleFile(files[0]);
			}
		});

		// 避免把檔案拖到頁面其他地方時瀏覽器直接開啟圖片
		['dragover', 'drop'].forEach((type) => {
			window.addEventListener(type, (e) => e.preventDefault());
		});

		this.dom.pauseButton.addEventListener('click', () => this.togglePause());
		this.dom.restartButton.addEventListener('click', () => this.restart());
		this.dom.restartApplication.addEventListener('click', () => this.restart());
	}

	/* ------------------------------------------------------------------ */
	/* 開始視窗                                                            */
	/* ------------------------------------------------------------------ */

	/**
	 * 處理使用者選擇的檔案：驗證格式並顯示預覽。
	 * @param {File} file
	 */
	async handleFile(file) {
		if (!ImageSlicer.isJpeg(file)) {
			this.selectedImage = null;
			this.dom.dropPreview.style.backgroundImage = '';
			this.showError('image', 'Only JPG images are accepted. Please drop a .jpg file.');
			return;
		}
		try {
			const dataUrl = await ImageSlicer.readFile(file);
			// 先確認確實可以被解碼成圖片
			await ImageSlicer.loadImage(dataUrl);
			this.selectedImage = dataUrl;
			this.dom.dropPreview.style.backgroundImage = 'url(' + dataUrl + ')';
			this.dom.dropText.innerHTML = 'Image ready: <strong>' + PuzzleApp.escapeHtml(file.name) + '</strong>'
				+ '<br><small>Drop another JPG to replace it</small>';
			this.clearError('image');
		} catch (err) {
			this.selectedImage = null;
			this.showError('image', err.message || 'The image could not be loaded.');
		}
	}

	/** 驗證表單並開始遊戲 */
	async handleStart() {
		const name = this.dom.nameInput.value.trim();
		const difficultId = parseInt(this.dom.difficultSelect.value, 10);
		let valid = true;

		if (name === '') {
			this.showError('name', 'Please enter your name to start.');
			valid = false;
		} else if (name.length > 60) {
			this.showError('name', 'The name must have 60 characters at most.');
			valid = false;
		} else {
			this.clearError('name');
		}

		if (!this.levels[difficultId]) {
			this.showError('difficult', 'Please choose a difficulty level.');
			valid = false;
		} else {
			this.clearError('difficult');
		}

		if (!this.selectedImage) {
			this.showError('image', 'Please drop a JPG image to build your puzzle.');
			valid = false;
		} else {
			this.clearError('image');
		}

		if (!valid) {
			return;
		}

		this.dom.startModal.classList.add('hide');
		await this.startGame({
			name: name,
			difficultId: difficultId,
			grid: this.levels[difficultId].grid,
			source: this.selectedImage
		});
	}

	showError(field, message) {
		const el = this.dom[field + 'Error'];
		if (el) {
			el.textContent = message;
			el.classList.add('show');
			el.closest('.group').classList.add('invalid');
		}
	}

	clearError(field) {
		const el = this.dom[field + 'Error'];
		if (el) {
			el.textContent = '';
			el.classList.remove('show');
			el.closest('.group').classList.remove('invalid');
		}
	}

	/* ------------------------------------------------------------------ */
	/* 遊戲流程                                                            */
	/* ------------------------------------------------------------------ */

	/**
	 * 開始一局新遊戲。
	 * @param {{name:string, difficultId:number, grid:number, source:string}} options
	 */
	async startGame(options) {
		// 置中裁切成正方形，尺寸與遊戲區域一致（500 x 500）
		const image = await ImageSlicer.cropCenteredSquare(options.source, 500);

		this.config = {
			name: options.name,
			difficultId: options.difficultId,
			grid: options.grid,
			image: image
		};

		this.renderGameChrome();
		await this.puzzle.build(image, options.grid);

		this.setPaused(false);
		this.timer.start(0);
		this.dom.gameContainer.classList.remove('hide');
		this.dom.endModal.classList.add('hide');
		this.saveState();
	}

	/** 更新遊戲畫面上的靜態資訊 */
	renderGameChrome() {
		this.dom.playerName.textContent = this.config.name;
		this.dom.preview.style.backgroundImage = 'url(' + this.config.image + ')';
	}

	/** 暫停 / 繼續 */
	togglePause() {
		this.setPaused(!this.paused);
		this.saveState();
	}

	/**
	 * @param {boolean} paused
	 */
	setPaused(paused) {
		this.paused = paused;
		this.puzzle.setPaused(paused);
		this.dom.puzzleContainer.classList.toggle('paused', paused);
		this.dom.pauseOverlay.classList.toggle('hide', !paused);
		this.dom.pauseButton.textContent = paused ? 'RESUME' : 'PAUSE';

		if (paused) {
			this.timer.pause();
		} else if (this.config && !this.puzzle.finished) {
			this.timer.resume();
		}
	}

	/** 重新開始：關閉所有視窗並回到開始視窗 */
	restart() {
		// 先清掉設定，避免 setPaused(false) 又把計時器接續跑起來
		this.config = null;
		this.timer.stop();
		this.timer.reset();
		this.puzzle.clear();
		this.puzzle.finished = false;
		this.setPaused(false);
		this.storage.clear();

		this.dom.gameContainer.classList.add('hide');
		this.dom.endModal.classList.add('hide');
		this.dom.startModal.classList.remove('hide');
	}

	/** 拼圖完成 */
	async handleSolved() {
		this.timer.stop();
		const seconds = this.timer.seconds;
		this.storage.clear();

		this.dom.endSummary.textContent = 'You solved the ' + this.levels[this.config.difficultId].name
			+ ' puzzle in ' + GameTimer.format(seconds * 1000) + '!';

		try {
			const data = await this.ranking.save({
				name: this.config.name,
				difficultId: this.config.difficultId,
				seconds: seconds
			});
			RankingService.render(this.dom.rankBody, data);
			this.dom.rankMessage.textContent = data.meInTop
				? 'You are in the top 3 of the ' + data.level + ' level!'
				: 'Top 3 of the ' + data.level + ' level, plus your own result.';
		} catch (err) {
			this.dom.rankBody.innerHTML = '';
			this.dom.rankMessage.textContent = 'The result could not be saved: ' + err.message;
		}

		this.dom.endModal.classList.remove('hide');
	}

	/* ------------------------------------------------------------------ */
	/* 存檔 / 還原                                                         */
	/* ------------------------------------------------------------------ */

	/** 節流版本的存檔（計時器每次 tick 都會呼叫） */
	throttledSave() {
		const now = Date.now();
		if (now - this.lastSaveAt < 1000) {
			return;
		}
		this.saveState();
	}

	/** 把目前狀態寫入 localStorage */
	saveState() {
		if (!this.config || this.puzzle.finished) {
			return;
		}
		this.lastSaveAt = Date.now();
		this.storage.save({
			version: 1,
			name: this.config.name,
			difficultId: this.config.difficultId,
			grid: this.config.grid,
			image: this.config.image,
			elapsed: this.timer.value,
			paused: this.paused,
			pieces: this.puzzle.serialize()
		});
	}

	/** 頁面載入時嘗試還原上一次未完成的遊戲 */
	async restoreFromStorage() {
		const state = this.storage.load();
		if (!state || !this.levels[state.difficultId]) {
			return;
		}

		this.config = {
			name: state.name,
			difficultId: state.difficultId,
			grid: state.grid,
			image: state.image
		};

		this.renderGameChrome();
		await this.puzzle.build(state.image, state.grid, state.pieces);

		this.dom.startModal.classList.add('hide');
		this.dom.gameContainer.classList.remove('hide');

		this.timer.start(state.elapsed || 0);
		if (state.paused) {
			this.setPaused(true);
		}
	}

	/** 簡單的 HTML 逸出，避免檔名注入標記 */
	static escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}
}

document.addEventListener('DOMContentLoaded', () => {
	window.puzzleApp = new PuzzleApp();
});
