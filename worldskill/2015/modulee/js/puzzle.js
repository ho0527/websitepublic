/**
 * Puzzle
 * 拼圖盤面：切片、洗牌、選取、旋轉、拖放判定與完成偵測。
 */
class Puzzle {

	/**
	 * @param {Object} options
	 * @param {HTMLElement} options.startEl    起始區（打散的碎片）
	 * @param {HTMLElement} options.destEl     目標區（半透明原圖）
	 * @param {number}      [options.size=500] 兩個區域的邊長（正方形）
	 * @param {Function}    [options.onChange] 盤面有變動時的回呼（用於存檔）
	 * @param {Function}    [options.onSolved] 拼圖完成時的回呼
	 */
	constructor(options) {
		this.startEl = options.startEl;
		this.destEl = options.destEl;
		this.size = options.size || 500;
		this.onChange = options.onChange || function () {};
		this.onSolved = options.onSolved || function () {};

		this.n = 0;
		this.pieceSize = 0;
		this.image = '';
		this.pieces = [];
		this.slots = [];
		this.selectedPiece = null;
		this.paused = false;
		this.finished = false;

		// 拖曳狀態
		this.drag = null;

		this.startEl.dataset.label = 'Starting area';
		this.destEl.dataset.label = 'Destination area';

		this.bindEvents();
	}

	/** 已正確放置的碎片數量 */
	get placedCount() {
		return this.pieces.filter((piece) => piece.placed).length;
	}

	/** 是否已完成 */
	get solved() {
		return this.pieces.length > 0 && this.placedCount === this.pieces.length;
	}

	/* ------------------------------------------------------------------ */
	/* 建立盤面                                                            */
	/* ------------------------------------------------------------------ */

	/**
	 * 依圖片與難度建立一局新遊戲。
	 * @param {string} image 已置中裁切成正方形的圖片 Data URL
	 * @param {number} n     每邊的塊數（2 / 3 / 4）
	 * @param {Object[]} [saved] 若提供，則以存檔資料還原碎片狀態
	 */
	async build(image, n, saved) {
		this.clear();
		this.image = image;
		this.n = n;
		this.pieceSize = this.size / n;
		this.finished = false;

		this.buildSlots();

		const slices = await ImageSlicer.slice(image, n);
		this.pieces = slices.map((slice, index) => new Piece({
			index: index,
			image: slice,
			size: this.pieceSize
		}));

		if (saved && saved.length === this.pieces.length) {
			this.applySavedState(saved);
		} else {
			this.shuffle();
		}

		this.onChange();
	}

	/** 建立目標區的半透明預覽格 */
	buildSlots() {
		this.slots = [];
		const total = this.n * this.n;
		for (let i = 0; i < total; i += 1) {
			const row = Math.floor(i / this.n);
			const col = i % this.n;
			const slot = document.createElement('div');
			slot.className = 'slot';
			slot.style.width = this.pieceSize + 'px';
			slot.style.height = this.pieceSize + 'px';
			slot.style.left = col * this.pieceSize + 'px';
			slot.style.top = row * this.pieceSize + 'px';
			slot.style.backgroundImage = 'url(' + this.image + ')';
			slot.style.backgroundSize = this.size + 'px ' + this.size + 'px';
			slot.style.backgroundPosition = (-col * this.pieceSize) + 'px ' + (-row * this.pieceSize) + 'px';
			this.destEl.appendChild(slot);
			this.slots.push(slot);
		}
	}

	/**
	 * 把碎片隨機打散到起始區的格點上（不重疊），並隨機旋轉 90 度的倍數。
	 */
	shuffle() {
		const cells = this.pieces.map((_, i) => i);
		Puzzle.shuffleArray(cells);

		this.pieces.forEach((piece, i) => {
			const cell = cells[i];
			const row = Math.floor(cell / this.n);
			const col = cell % this.n;
			piece.setStartPosition(col * this.pieceSize, row * this.pieceSize);
			piece.angle = Puzzle.randomInt(0, 3) * 90;
			piece.applyTransform();
			this.startEl.appendChild(piece.el);
		});

		// 避免極小機率下「一開局就已完成」：若全部都在正確位置且未旋轉，重洗一次
		const alreadySolved = this.pieces.every((piece) => {
			const cellIndex = Math.round(piece.startY / this.pieceSize) * this.n
				+ Math.round(piece.startX / this.pieceSize);
			return cellIndex === piece.index && piece.isUpright;
		});
		if (alreadySolved && this.pieces.length > 1) {
			this.startEl.innerHTML = '';
			this.shuffle();
		}
	}

	/**
	 * 以存檔資料還原碎片。
	 * @param {Object[]} saved
	 */
	applySavedState(saved) {
		this.pieces.forEach((piece, i) => {
			piece.restore(saved[i]);
			if (piece.placed) {
				const row = Math.floor(piece.index / this.n);
				const col = piece.index % this.n;
				piece.el.style.left = col * this.pieceSize + 'px';
				piece.el.style.top = row * this.pieceSize + 'px';
				piece.el.classList.add('placed');
				piece.angle = 0;
				piece.applyTransform();
				this.destEl.appendChild(piece.el);
				this.slots[piece.index].classList.add('filled');
			} else {
				piece.setStartPosition(piece.startX, piece.startY);
				piece.applyTransform();
				this.startEl.appendChild(piece.el);
			}
		});
	}

	/** 清空盤面 */
	clear() {
		this.selectedPiece = null;
		this.drag = null;
		this.pieces = [];
		this.slots = [];
		this.startEl.innerHTML = '';
		this.destEl.innerHTML = '';
	}

	/* ------------------------------------------------------------------ */
	/* 事件                                                                */
	/* ------------------------------------------------------------------ */

	bindEvents() {
		this.startEl.addEventListener('pointerdown', (e) => this.onPointerDown(e));
		window.addEventListener('pointermove', (e) => this.onPointerMove(e));
		window.addEventListener('pointerup', (e) => this.onPointerUp(e));
		window.addEventListener('pointercancel', (e) => this.onPointerUp(e));
		document.addEventListener('keydown', (e) => this.onKeyDown(e));
	}

	/** 是否可以互動（暫停或已完成時不可） */
	get interactive() {
		return !this.paused && !this.finished;
	}

	onPointerDown(e) {
		if (!this.interactive) {
			return;
		}
		const el = e.target.closest ? e.target.closest('.piece') : null;
		if (!el || !el.piece || el.piece.placed) {
			return;
		}
		e.preventDefault();

		const piece = el.piece;
		this.selectPiece(piece);

		const rect = el.getBoundingClientRect();
		this.drag = {
			piece: piece,
			offsetX: e.clientX - rect.left,
			offsetY: e.clientY - rect.top,
			originAngle: piece.angle,
			startClientX: e.clientX,
			startClientY: e.clientY,
			moving: false
		};
	}

	onPointerMove(e) {
		if (!this.drag || !this.interactive) {
			return;
		}
		const dx = e.clientX - this.drag.startClientX;
		const dy = e.clientY - this.drag.startClientY;

		if (!this.drag.moving) {
			// 超過門檻才視為拖曳（避免單純點選被誤判）
			if (Math.abs(dx) < 4 && Math.abs(dy) < 4) {
				return;
			}
			this.beginDrag();
		}

		const el = this.drag.piece.el;
		el.style.left = (e.clientX - this.drag.offsetX) + 'px';
		el.style.top = (e.clientY - this.drag.offsetY) + 'px';
	}

	/** 把碎片改為隨滑鼠移動的浮動狀態 */
	beginDrag() {
		const piece = this.drag.piece;
		const rect = piece.el.getBoundingClientRect();
		this.drag.moving = true;
		piece.el.classList.add('dragging');
		document.body.appendChild(piece.el);
		piece.el.style.left = rect.left + 'px';
		piece.el.style.top = rect.top + 'px';
	}

	onPointerUp(e) {
		if (!this.drag) {
			return;
		}
		const drag = this.drag;
		this.drag = null;

		if (!drag.moving) {
			return; // 只是點選，不做放置判定
		}
		this.dropPiece(drag, e);
	}

	/**
	 * 放開碎片時的判定。
	 * @param {Object} drag
	 */
	dropPiece(drag) {
		const piece = drag.piece;
		const rect = piece.el.getBoundingClientRect();
		const centerX = rect.left + rect.width / 2;
		const centerY = rect.top + rect.height / 2;
		const target = this.destEl.getBoundingClientRect();

		const insideDestination = centerX >= target.left && centerX <= target.right
			&& centerY >= target.top && centerY <= target.bottom;

		if (insideDestination) {
			const col = Puzzle.clamp(Math.floor((centerX - target.left) / this.pieceSize), 0, this.n - 1);
			const row = Puzzle.clamp(Math.floor((centerY - target.top) / this.pieceSize), 0, this.n - 1);
			const cellIndex = row * this.n + col;

			// 位置與旋轉都正確才接受
			if (cellIndex === piece.index && piece.isUpright) {
				this.acceptPiece(piece, row, col);
				return;
			}
		}

		this.rejectPiece(piece, drag.originAngle);
	}

	/** 接受碎片：吸附到格子並顯色 */
	acceptPiece(piece, row, col) {
		piece.el.classList.remove('dragging');
		this.destEl.appendChild(piece.el);
		piece.el.style.left = col * this.pieceSize + 'px';
		piece.el.style.top = row * this.pieceSize + 'px';
		piece.angle = 0;
		piece.markPlaced();
		this.slots[piece.index].classList.add('filled');
		this.selectedPiece = null;

		this.onChange();

		if (this.solved) {
			this.finished = true;
			this.onSolved();
		}
	}

	/** 拒絕碎片：以動畫回到原本的位置與旋轉 */
	rejectPiece(piece, originAngle) {
		const startRect = this.startEl.getBoundingClientRect();
		piece.angle = originAngle;
		piece.deselect();
		piece.el.classList.add('returning', 'rejected');
		piece.el.style.left = (startRect.left + piece.startX) + 'px';
		piece.el.style.top = (startRect.top + piece.startY) + 'px';

		window.setTimeout(() => {
			piece.el.classList.remove('dragging', 'returning', 'rejected');
			this.startEl.appendChild(piece.el);
			piece.setStartPosition(piece.startX, piece.startY);
			piece.applyTransform();
		}, 340);

		this.onChange();
	}

	/**
	 * 選取碎片（其他碎片自動取消選取）。
	 * @param {Piece} piece
	 */
	selectPiece(piece) {
		if (this.selectedPiece && this.selectedPiece !== piece) {
			this.selectedPiece.deselect();
		}
		this.selectedPiece = piece;
		piece.select();
	}

	onKeyDown(e) {
		if (!this.interactive || !this.selectedPiece || this.selectedPiece.placed) {
			return;
		}
		if (e.key === 'ArrowRight') {
			e.preventDefault();
			this.selectedPiece.rotate(1);   // 順時針
			this.onChange();
		} else if (e.key === 'ArrowLeft') {
			e.preventDefault();
			this.selectedPiece.rotate(-1);  // 逆時針
			this.onChange();
		}
	}

	/**
	 * 暫停 / 繼續：暫停時隱藏兩個區域並停止所有互動。
	 * @param {boolean} paused
	 */
	setPaused(paused) {
		this.paused = paused;
		if (paused && this.drag) {
			this.rejectPiece(this.drag.piece, this.drag.originAngle);
			this.drag = null;
		}
	}

	/** 序列化目前所有碎片的狀態 */
	serialize() {
		return this.pieces.map((piece) => piece.toJSON());
	}

	/* ------------------------------------------------------------------ */
	/* 工具方法                                                            */
	/* ------------------------------------------------------------------ */

	/** Fisher-Yates 洗牌 */
	static shuffleArray(array) {
		for (let i = array.length - 1; i > 0; i -= 1) {
			const j = Math.floor(Math.random() * (i + 1));
			const tmp = array[i];
			array[i] = array[j];
			array[j] = tmp;
		}
		return array;
	}

	static randomInt(min, max) {
		return min + Math.floor(Math.random() * (max - min + 1));
	}

	static clamp(value, min, max) {
		return Math.min(max, Math.max(min, value));
	}
}

window.Puzzle = Puzzle;
