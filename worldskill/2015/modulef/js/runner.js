/**
 * Runner
 * 聖火跑者：位置、換道、跳躍、爬坡與各種動畫狀態。
 */
class Runner {

	/**
	 * @param {HTMLElement} el       #runner 容器
	 * @param {HTMLElement} spriteEl #runnerSprite 影格顯示元素
	 */
	constructor(el, spriteEl) {
		this.el = el;
		this.spriteEl = spriteEl;
		this.reset();
	}

	/** 回到起跑狀態 */
	reset() {
		this.x = GameConfig.startX;
		this.laneIndex = 2;          // 由第 3 跑道（最下方）起跑
		this.jumping = false;
		this.jumpStartedAt = 0;
		this.jumpOffset = 0;         // 跳躍造成的離地高度
		this.climbing = false;
		this.elevation = 0;          // 爬坡造成的高度增加
		this.frameIndex = 0;
		this.frameTime = 0;
		this.state = 'idle';

		this.el.className = 'idle';
		this.spriteEl.style.backgroundImage = 'url(imgs/runner.svg)';
		this.applyTransform();
	}

	/** 目前所在的跑道設定 */
	get lane() {
		return GameConfig.lanes[this.laneIndex];
	}

	/** 目前跑道編號（1 ~ 3） */
	get laneNumber() {
		return this.lane.id;
	}

	/** 目前是否在地面上（沒有跳躍中） */
	get onGround() {
		return !this.jumping;
	}

	/** 目前的縮放比例（含爬坡時往山頂收斂） */
	get scale() {
		if (this.elevationRatio > 0) {
			return Runner.lerp(this.lane.scale, GameConfig.plateauScale, this.elevationRatio);
		}
		return this.lane.scale;
	}

	/** 爬坡進度（0 = 還在平地，1 = 已到山頂） */
	get elevationRatio() {
		const total = GameConfig.plateauBottom - this.lane.bottom;
		return total <= 0 ? 0 : Math.min(1, Math.max(0, this.elevation / total));
	}

	/** 目前腳底離 #playground 底部的距離 */
	get bottom() {
		return this.lane.bottom + this.elevation + this.jumpOffset;
	}

	/** 開始奔跑 */
	startRunning() {
		this.state = 'running';
		this.el.className = 'running';
	}

	/**
	 * 換道。
	 * @param {number} delta -1 = 往上一條（編號變小），1 = 往下一條
	 * @returns {boolean} 是否真的換了道
	 */
	changeLane(delta) {
		const target = this.laneIndex + delta;
		// 不可跑出三條跑道之外
		if (target < 0 || target > GameConfig.lanes.length - 1) {
			return false;
		}
		this.laneIndex = target;
		this.applyTransform();
		return true;
	}

	/**
	 * 起跳（只有在地面上才能跳）。
	 * @returns {boolean}
	 */
	jump() {
		if (!this.onGround || this.state !== 'running') {
			return false;
		}
		this.jumping = true;
		this.jumpStartedAt = performance.now();
		this.el.classList.add('jumping');
		this.el.classList.remove('running');
		this.spriteEl.style.backgroundImage = 'url(' + GameConfig.jumpFrame + ')';
		return true;
	}

	/**
	 * 每一幀更新。
	 * @param {number} now   目前時間戳
	 * @param {number} delta 距離上一幀的毫秒數
	 */
	update(now, delta) {
		// --- 跳躍：拋物線 ---
		if (this.jumping) {
			const progress = (now - this.jumpStartedAt) / GameConfig.jumpDuration;
			if (progress >= 1) {
				this.jumping = false;
				this.jumpOffset = 0;
				this.el.classList.remove('jumping');
				this.el.classList.add(this.climbing ? 'climbing' : 'running');
			} else {
				// 4p(1-p) 的拋物線，最高點在中間
				this.jumpOffset = GameConfig.jumpHeight * 4 * progress * (1 - progress);
			}
		}

		// --- 奔跑影格 ---
		if (this.state === 'running' && !this.jumping) {
			this.frameTime += delta;
			if (this.frameTime >= GameConfig.frameDuration) {
				this.frameTime = 0;
				this.frameIndex = (this.frameIndex + 1) % GameConfig.runFrames.length;
				this.spriteEl.style.backgroundImage = 'url(' + GameConfig.runFrames[this.frameIndex] + ')';
			}
		}

		this.applyTransform();
	}

	/**
	 * 設定爬坡高度。
	 * @param {number} ratio 0 ~ 1
	 */
	setClimb(ratio) {
		const total = GameConfig.plateauBottom - this.lane.bottom;
		this.elevation = Math.max(0, total * ratio);
		const climbing = ratio > 0 && ratio < 1;
		if (climbing !== this.climbing) {
			this.climbing = climbing;
			if (!this.jumping) {
				this.el.classList.toggle('climbing', climbing);
				this.el.classList.toggle('running', !climbing && this.state === 'running');
			}
		}
	}

	/** 撞到障礙物 */
	crash() {
		this.state = 'crashed';
		this.el.className = 'crashed';
	}

	/** 抵達聖火台 */
	celebrate() {
		this.state = 'finished';
		this.el.className = 'celebrating';
		this.spriteEl.style.backgroundImage = 'url(imgs/runner.svg)';
	}

	/** 把目前狀態寫入 DOM */
	applyTransform() {
		this.el.style.left = this.x + 'px';
		this.el.style.bottom = this.bottom + 'px';
		this.el.style.transform = 'scale(' + this.scale.toFixed(3) + ')';
		// 越靠近鏡頭（下方跑道）疊在越上層
		this.el.style.zIndex = String(30 + this.laneIndex * 5 + 1);
	}

	/** 碰撞用的水平半寬（已套用縮放） */
	get halfWidth() {
		return GameConfig.runnerHalfWidth * this.scale;
	}

	static lerp(a, b, t) {
		return a + (b - a) * t;
	}
}

window.Runner = Runner;
