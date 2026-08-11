/**
 * GameConfig
 * 遊戲的所有幾何與時間常數集中在這裡，方便調校。
 * 座標系統：以 #playground（5632 x 635）為基準，
 *           x 從左至右，垂直方向使用「距離底部的距離」（bottom）。
 */
const GameConfig = {

	/** 賽道總寬度（與 #game 的寬度一致） */
	worldWidth: 5632,

	/** 賽道高度（與 #game 的高度一致） */
	worldHeight: 635,

	/** 跑者起跑位置 */
	startX: 60,

	/** 上坡起點 / 終點（對照 ground.svg 的斜坡位置） */
	hillStart: 4630,
	hillEnd: 5095,

	/** 跑者停下來的位置（聖火台旁的白色平台） */
	stopX: 5140,

	/** 全程跑完所需時間（毫秒）：題目要求 9 ~ 12 秒 */
	raceDuration: 10500,

	/** 平台（山頂）的地面高度 */
	plateauBottom: 232,

	/** 山頂上跑者的縮放 */
	plateauScale: 0.80,

	/**
	 * 三條跑道（1 = 最上方 / 最遠，3 = 最下方 / 最近）
	 * bottom：地面基準線；scale：透視縮放（越近越大）
	 */
	lanes: [
		{ id: 1, bottom: 112, scale: 0.62 },
		{ id: 2, bottom: 82,  scale: 0.78 },
		{ id: 3, bottom: 52,  scale: 0.96 }
	],

	/** 跳躍 */
	jumpHeight: 105,
	jumpDuration: 620,

	/** 奔跑動畫的每張影格時間（毫秒） */
	frameDuration: 80,

	/** 奔跑影格（主辦單位提供於 media/runner） */
	runFrames: [
		'runner/runner_1.png',
		'runner/runner_4.png',
		'runner/runner_3.png',
		'runner/runner_2.png'
	],

	/** 跳躍姿勢（雙腿張開） */
	jumpFrame: 'runner/runner_1.png',

	/** 障礙物數量與可放置的範圍 */
	obstacleCount: 6,
	obstacleRangeStart: 900,
	obstacleRangeEnd: 4550,
	obstacleMinGap: 320,

	/** 各跑道障礙物的尺寸（與 CSS 一致） */
	obstacleSize: {
		1: { width: 16, height: 62 },
		2: { width: 22, height: 68 },
		3: { width: 28, height: 74 }
	},

	/** 跑者的碰撞半寬（未縮放前） */
	runnerHalfWidth: 26,

	/**
	 * 五個地標，依出現順序排列。
	 * triggerX：跑者跑到這個 x 座標時開始播放呈現動畫（= 招牌左緣）
	 */
	landmarks: [
		{ key: 'amazon',   panel: 'amazonPanel',   name: 'Amazon Rainforest - Manaus - AM' },
		{ key: 'bahia',    panel: 'bahiaPanel',    name: 'Lacerda Elevator - Salvador - BA' },
		{ key: 'parana',   panel: 'paranaPanel',   name: 'Iguacu Falls - Foz do Iguacu - PR' },
		{ key: 'saopaulo', panel: 'saopauloPanel', name: 'Cable-Stayed Bridge - Sao Paulo - SP' },
		{ key: 'rio',      panel: 'rioPanel',      name: 'Christ the Redeemer - Rio de Janeiro - RJ' }
	],

	/** 版面把整條賽道切成 7 等份，每個地標區塊的寬度 */
	get sectionWidth() {
		return this.worldWidth / 7;
	},

	/**
	 * 第 index 個地標的觸發位置（招牌左緣）。
	 * 第 0 個地標的區塊從第 1 份開始，因此左緣 = sectionWidth * (index + 1)。
	 */
	landmarkTriggerX(index) {
		return this.sectionWidth * (index + 1);
	}
};

window.GameConfig = GameConfig;
