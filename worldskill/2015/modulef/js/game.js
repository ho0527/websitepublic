/**
 * OlympicRace
 * 遊戲主控制器：主迴圈、鏡頭、鍵盤控制、碰撞判定、地標觸發與結束流程。
 */
class OlympicRace {

	constructor() {
		this.dom = {
			game: document.getElementById('game'),
			playground: document.getElementById('playground'),
			runner: document.getElementById('runner'),
			runnerSprite: document.getElementById('runnerSprite'),
			runways: [
				document.getElementById('runway1'),
				document.getElementById('runway2'),
				document.getElementById('runway3')
			],
			pyreFire: document.getElementById('pyreFire'),
			startModal: document.getElementById('start'),
			endModal: document.getElementById('end'),
			endError: document.querySelector('#end .error'),
			endSuccess: document.querySelector('#end .success'),
			startButton: document.getElementById('startButton'),
			restartButton: document.getElementById('restartButton'),
			progressBar: document.getElementById('progressBar')
		};

		this.runner = new Runner(this.dom.runner, this.dom.runnerSprite);
		this.landmarks = GameConfig.landmarks.map((def, i) => new Landmark(def, i));
		this.obstacles = [];

		this.running = false;
		this.finished = false;
		this.rafId = null;
		this.lastFrame = 0;

		this.bindEvents();
		this.prepare();
	}

	/* ------------------------------------------------------------------ */
	/* 初始化                                                              */
	/* ------------------------------------------------------------------ */

	bindEvents() {
		this.dom.startButton.addEventListener('click', (e) => {
			e.preventDefault();
			this.start();
		});
		this.dom.restartButton.addEventListener('click', (e) => {
			e.preventDefault();
			this.start();
		});
		document.addEventListener('keydown', (e) => this.onKeyDown(e));
		window.addEventListener('resize', () => this.updateCamera());
	}

	/** 佈置一場新比賽（尚未開跑） */
	prepare() {
		this.finished = false;
		this.running = false;

		this.runner.reset();
		this.landmarks.forEach((landmark) => landmark.reset());

		this.dom.pyreFire.classList.remove('lit');
		this.dom.game.classList.remove('lit');

		this.buildObstacles();
		this.updateCamera();
		this.updateProgress();
	}

	/** 隨機產生障礙物並放進對應的跑道 */
	buildObstacles() {
		this.dom.runways.forEach((runway) => {
			runway.innerHTML = '';
		});
		this.obstacles = Obstacle.generate();
		this.obstacles.forEach((obstacle) => {
			this.dom.runways[obstacle.laneIndex].appendChild(obstacle.el);
		});
	}

	/* ------------------------------------------------------------------ */
	/* 遊戲流程                                                            */
	/* ------------------------------------------------------------------ */

	/** 開始（或重新開始）一場比賽 */
	start() {
		this.stopLoop();
		this.prepare();

		this.dom.startModal.classList.add('hide');
		this.dom.endModal.classList.add('hide');

		this.runner.startRunning();
		this.running = true;
		this.startedAt = performance.now();
		this.lastFrame = this.startedAt;
		this.rafId = window.requestAnimationFrame((t) => this.loop(t));
	}

	stopLoop() {
		if (this.rafId) {
			window.cancelAnimationFrame(this.rafId);
			this.rafId = null;
		}
		this.running = false;
	}

	/**
	 * 主迴圈。
	 * @param {number} now
	 */
	loop(now) {
		if (!this.running) {
			return;
		}
		const delta = Math.min(64, now - this.lastFrame);
		this.lastFrame = now;

		this.advanceRunner(now);
		this.runner.update(now, delta);
		this.updateLandmarks();
		this.updateCamera();
		this.updateProgress();

		if (this.checkCollision()) {
			this.gameOver();
			return;
		}

		if (this.runner.x >= GameConfig.stopX) {
			this.win();
			return;
		}

		this.rafId = window.requestAnimationFrame((t) => this.loop(t));
	}

	/**
	 * 依經過時間推進跑者，並處理上坡。
	 * @param {number} now
	 */
	advanceRunner(now) {
		const elapsed = now - this.startedAt;
		const distance = GameConfig.stopX - GameConfig.startX;
		const progress = Math.min(1, elapsed / GameConfig.raceDuration);
		this.runner.x = GameConfig.startX + distance * progress;

		// 上坡：把腳底高度由跑道基準線平滑地拉升到山頂平台
		const { hillStart, hillEnd } = GameConfig;
		if (this.runner.x <= hillStart) {
			this.runner.setClimb(0);
		} else if (this.runner.x >= hillEnd) {
			this.runner.setClimb(1);
		} else {
			const t = (this.runner.x - hillStart) / (hillEnd - hillStart);
			// 使用 smoothstep 讓上坡的起訖更自然
			this.runner.setClimb(t * t * (3 - 2 * t));
		}
	}

	/** 鏡頭：讓跑者維持在畫面左側 1/4 處，並限制在賽道範圍內 */
	updateCamera() {
		const viewport = window.innerWidth;
		const anchor = Math.min(320, viewport * 0.28);
		const max = Math.max(0, GameConfig.worldWidth - viewport);
		const camera = Math.min(max, Math.max(0, this.runner.x - anchor));
		this.dom.game.style.transform = 'translate3d(' + (-camera) + 'px,0,0)';
	}

	/** 更新進度列 */
	updateProgress() {
		const total = GameConfig.stopX - GameConfig.startX;
		const done = (this.runner.x - GameConfig.startX) / total;
		this.dom.progressBar.style.width = (Math.min(1, Math.max(0, done)) * 100).toFixed(1) + '%';
	}

	/** 檢查是否有地標需要呈現 */
	updateLandmarks() {
		this.landmarks.forEach((landmark) => landmark.update(this.runner.x));
	}

	/**
	 * 碰撞判定：同一條跑道、水平重疊，而且跳躍高度不足以跨過障礙物。
	 * @returns {boolean}
	 */
	checkCollision() {
		const runner = this.runner;
		for (let i = 0; i < this.obstacles.length; i += 1) {
			const obstacle = this.obstacles[i];
			if (obstacle.hit || obstacle.laneIndex !== runner.laneIndex) {
				continue;
			}
			const overlap = Math.abs(runner.x - obstacle.x) < (runner.halfWidth + obstacle.halfWidth);
			if (!overlap) {
				continue;
			}
			// 跳得夠高就閃過（同時也可以靠換道閃避）
			if (runner.jumpOffset >= obstacle.height * 0.62) {
				obstacle.hit = true;
				continue;
			}
			return true;
		}
		return false;
	}

	/* ------------------------------------------------------------------ */
	/* 控制                                                                */
	/* ------------------------------------------------------------------ */

	onKeyDown(e) {
		if (!this.running) {
			// 未開始時，Enter / 空白鍵可直接開始
			if ((e.key === 'Enter' || e.key === ' ') && !this.dom.startModal.classList.contains('hide')) {
				e.preventDefault();
				this.start();
			}
			return;
		}

		if (e.key === 'ArrowUp') {
			e.preventDefault();
			this.runner.changeLane(-1);   // 往上一條跑道
		} else if (e.key === 'ArrowDown') {
			e.preventDefault();
			this.runner.changeLane(1);    // 往下一條跑道
		} else if (e.key === ' ' || e.code === 'Space') {
			e.preventDefault();
			this.runner.jump();           // 只有在地面上才會起跳
		}
	}

	/* ------------------------------------------------------------------ */
	/* 結束                                                                */
	/* ------------------------------------------------------------------ */

	/** 撞到障礙物 */
	gameOver() {
		this.stopLoop();
		this.finished = true;
		this.runner.crash();

		this.dom.endError.classList.remove('hide');
		this.dom.endSuccess.classList.add('hide');
		window.setTimeout(() => this.dom.endModal.classList.remove('hide'), 550);
	}

	/** 抵達聖火台：點燃聖火並顯示成功訊息 */
	win() {
		this.stopLoop();
		this.finished = true;
		this.runner.x = GameConfig.stopX;
		this.runner.setClimb(1);
		this.runner.celebrate();
		this.updateCamera();
		this.updateProgress();

		// 點燃聖火
		window.setTimeout(() => {
			this.dom.pyreFire.classList.add('lit');
			this.dom.game.classList.add('lit');
		}, 350);

		this.dom.endError.classList.add('hide');
		this.dom.endSuccess.classList.remove('hide');
		window.setTimeout(() => this.dom.endModal.classList.remove('hide'), 1700);
	}
}

window.OlympicRace = OlympicRace;
