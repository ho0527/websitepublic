/**
 * Piece
 * 單一拼圖碎片。負責自身的 DOM、旋轉角度、在起始區的座標與狀態序列化。
 */
class Piece {

	/**
	 * @param {Object} options
	 * @param {number} options.index    正確位置的索引（0 起算，由左上到右下）
	 * @param {string} options.image    碎片圖片的 Data URL
	 * @param {number} options.size     顯示尺寸（px）
	 */
	constructor(options) {
		this.index = options.index;
		this.image = options.image;
		this.size = options.size;

		this.angle = 0;        // 目前角度（累加值，方便做連續旋轉動畫）
		this.startX = 0;       // 在起始區的 left
		this.startY = 0;       // 在起始區的 top
		this.placed = false;   // 是否已正確放入目標區
		this.selected = false;

		this.el = this.createElement();
	}

	/** 目前角度換算成 0/90/180/270 */
	get normalizedAngle() {
		return ((this.angle % 360) + 360) % 360;
	}

	/** 是否處於正確的旋轉方向 */
	get isUpright() {
		return this.normalizedAngle === 0;
	}

	/** 建立 DOM 元素 */
	createElement() {
		const el = document.createElement('div');
		el.className = 'piece';
		el.dataset.index = String(this.index);
		el.style.width = this.size + 'px';
		el.style.height = this.size + 'px';
		el.style.backgroundImage = 'url(' + this.image + ')';
		el.piece = this; // 反向參照，方便事件處理
		return el;
	}

	/**
	 * 放到起始區的指定座標。
	 * @param {number} x
	 * @param {number} y
	 */
	setStartPosition(x, y) {
		this.startX = x;
		this.startY = y;
		this.el.style.left = x + 'px';
		this.el.style.top = y + 'px';
	}

	/** 依目前狀態套用 transform（旋轉 + 選取時的抬升） */
	applyTransform() {
		const scale = this.selected && !this.placed ? 1.12 : 1;
		this.el.style.setProperty('--rot', this.angle + 'deg');
		this.el.style.transform = 'rotate(' + this.angle + 'deg) scale(' + scale + ')';
	}

	/**
	 * 旋轉 90 度。
	 * @param {number} direction 1 = 順時針，-1 = 逆時針
	 */
	rotate(direction) {
		this.angle += direction * 90;
		this.applyTransform();
	}

	/** 選取（抬升到最上層，帶動畫） */
	select() {
		this.selected = true;
		this.el.classList.add('selected');
		this.applyTransform();
	}

	/** 取消選取 */
	deselect() {
		this.selected = false;
		this.el.classList.remove('selected');
		this.applyTransform();
	}

	/** 正確放置後的樣式與顯色動畫 */
	markPlaced() {
		this.placed = true;
		this.selected = false;
		this.el.classList.remove('selected', 'dragging', 'returning');
		this.el.classList.add('placed', 'revealing');
		this.applyTransform();
		window.setTimeout(() => this.el.classList.remove('revealing'), 700);
	}

	/** 序列化（存入 localStorage） */
	toJSON() {
		return {
			index: this.index,
			angle: this.angle,
			startX: this.startX,
			startY: this.startY,
			placed: this.placed
		};
	}

	/**
	 * 由序列化資料還原狀態。
	 * @param {Object} data
	 */
	restore(data) {
		this.angle = data.angle || 0;
		this.startX = data.startX || 0;
		this.startY = data.startY || 0;
		this.placed = !!data.placed;
	}
}

window.Piece = Piece;
