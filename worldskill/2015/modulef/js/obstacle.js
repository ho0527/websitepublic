/**
 * Obstacle
 * 賽道上的單一障礙物。
 */
class Obstacle {

	/**
	 * @param {number} x         在賽道上的位置
	 * @param {number} laneIndex 0 / 1 / 2（對應第 1 / 2 / 3 跑道）
	 */
	constructor(x, laneIndex) {
		this.x = x;
		this.laneIndex = laneIndex;
		this.size = GameConfig.obstacleSize[GameConfig.lanes[laneIndex].id];
		this.hit = false;

		this.el = document.createElement('span');
		this.el.className = 'obstacle';
		this.el.style.left = (x - this.size.width / 2) + 'px';
	}

	/** 障礙物的高度（跳過去需要的離地高度） */
	get height() {
		return this.size.height;
	}

	/** 碰撞用的水平半寬 */
	get halfWidth() {
		return this.size.width / 2;
	}

	/**
	 * 隨機產生一組障礙物：
	 * 至少 5 個、位置隨機、每條跑道至少一個、彼此不會太靠近。
	 *
	 * @returns {Obstacle[]}
	 */
	static generate() {
		const positions = [];
		let guard = 0;
		while (positions.length < GameConfig.obstacleCount && guard < 2000) {
			guard += 1;
			const x = Obstacle.randomBetween(GameConfig.obstacleRangeStart, GameConfig.obstacleRangeEnd);
			const tooClose = positions.some((p) => Math.abs(p - x) < GameConfig.obstacleMinGap);
			if (!tooClose) {
				positions.push(x);
			}
		}
		positions.sort((a, b) => a - b);

		// 先保證三條跑道各有一個，其餘隨機
		const lanes = [0, 1, 2];
		Obstacle.shuffle(lanes);
		const laneAssignment = positions.map((_, i) => (
			i < lanes.length ? lanes[i] : Math.floor(Math.random() * 3)
		));
		Obstacle.shuffle(laneAssignment);

		return positions.map((x, i) => new Obstacle(x, laneAssignment[i]));
	}

	static randomBetween(min, max) {
		return Math.round(min + Math.random() * (max - min));
	}

	static shuffle(array) {
		for (let i = array.length - 1; i > 0; i -= 1) {
			const j = Math.floor(Math.random() * (i + 1));
			const tmp = array[i];
			array[i] = array[j];
			array[j] = tmp;
		}
		return array;
	}
}

window.Obstacle = Obstacle;
