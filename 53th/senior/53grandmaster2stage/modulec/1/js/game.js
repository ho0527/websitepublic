/**
 * Star Battle - 遊戲引擎
 *
 * 主要規則（依試題）：
 *   燃料起始 15 點、上限 30 點，每秒消耗 1 點，歸零即遊戲結束
 *   計時器由 0 開始，顯示飛行秒數；分數由 0 開始，允許負分
 *   空白鍵射擊，一次按壓只能發射一發；子彈只能摧毀一個目標
 *   撞到小行星或飛船：該元素被摧毀且燃料 -15；被敵方子彈擊中：燃料 -15
 *   敵方飛船 1 發擊毀 +5 分；友方飛船被擊毀 -10 分；小行星 2 發擊毀 +10 分
 *   燃料圖示由上方隨機水平位置落下，收集 +15 點
 *   每 5 秒提高一次難度：元素出現更快、移動更快，敵方射擊更頻繁
 *   行星以不同速度移動形成視差效果（大顆較快）
 */
(function (global) {
    'use strict';

    /* ---------- 遊戲常數 ---------- */
    var FUEL_START = 15;          // 起始燃料
    var FUEL_MAX = 30;            // 燃料上限
    var FUEL_PER_SECOND = 1;      // 每秒消耗
    var FUEL_PENALTY = 15;        // 碰撞或被擊中的扣除量
    var FUEL_PICKUP = 15;         // 收集燃料的補充量

    var SCORE_ENEMY = 5;          // 擊毀敵方飛船
    var SCORE_ASTEROID = 10;      // 擊毀小行星
    var SCORE_FRIEND = -10;       // 誤擊友方飛船

    var PLAYER_SPEED = 280;       // 主飛船移動速度（px/秒）
    var PLAYER_SHOT_SPEED = 640;  // 主飛船子彈速度
    var ENEMY_SHOT_SPEED = 320;   // 敵方子彈速度

    var LEVEL_DURATION = 5;       // 每 5 秒提高一次難度
    var MAX_LEVEL = 10;           // 難度上限

    /** 小行星圖片（隨機挑選，讓畫面更有變化） */
    var ASTEROID_IMAGES = [
        'material/picture/aestroid_gray.png',
        'material/picture/aestroid_brown.png',
        'material/picture/aestroid_dark.png',
        'material/picture/aestroid_gray_2.png'
    ];

    /** 背景行星圖片與相對大小（大顆移動較快 → 視差） */
    var PLANET_IMAGES = [
        'material/picture/planets/001-global.png',
        'material/picture/planets/006-mars.png',
        'material/picture/planets/008-earth-globe.png',
        'material/picture/planets/009-saturn.png',
        'material/picture/planets/010-uranus.png',
        'material/picture/planets/012-jupiter.png',
        'material/picture/planets/002-travel.png',
        'material/picture/planets/011-planet-earth.png'
    ];

    /**
     * 遊戲主體
     * @param {Object} elements 由 main.js 傳入的 DOM 參考
     * @param {SoundManager} sound 音效管理器
     */
    function Game(elements, sound) {
        this.dom = elements;
        this.sound = sound;

        this.state = 'idle';      // idle | playing | paused | over
        this.entities = [];       // 場上所有飛行元素
        this.planets = [];        // 背景行星

        this.fuel = FUEL_START;
        this.score = 0;
        this.elapsed = 0;
        this.level = 1;

        this.player = { x: 60, y: 220, width: 78, height: 44 };
        this.directions = { up: false, down: false, left: false, right: false };
        this.canShoot = true;     // 空白鍵需放開後才能再次射擊

        this.spawnTimer = 0;
        this.fuelSpawnTimer = 0;
        this.enemyFireTimer = 0;

        this.lastFrameTime = 0;
        this.fieldWidth = 960;
        this.fieldHeight = 534;

        this.onGameOver = null;   // 由 main.js 指定的結束回呼

        this.createPlanets();
        this.loop = this.loop.bind(this);
        global.requestAnimationFrame(this.loop);
    }

    /* ====================================================================
       背景行星（視差）
       ==================================================================== */

    /** 建立背景行星，大顆的速度較快 */
    Game.prototype.createPlanets = function () {
        var layer = this.dom.planetLayer;
        var self = this;

        PLANET_IMAGES.forEach(function (source, index) {
            // 由小到大共 8 顆，尺寸決定速度
            var size = 24 + index * 12;
            var image = document.createElement('img');

            image.className = 'planet';
            image.src = source;
            image.alt = '';
            image.width = size;
            image.height = size;
            image.style.width = size + 'px';
            image.style.height = size + 'px';

            layer.appendChild(image);

            self.planets.push({
                element: image,
                x: Math.random() * 960,
                y: 20 + Math.random() * 480,
                size: size,
                // 尺寸越大速度越快（8 ~ 46 px/秒）
                speed: 8 + size * 0.28
            });
        });
    };

    /** 更新行星位置（由右往左，超出畫面後回到右側） */
    Game.prototype.updatePlanets = function (delta) {
        var self = this;

        this.planets.forEach(function (planet) {
            planet.x -= planet.speed * delta;

            if (planet.x + planet.size < 0) {
                planet.x = 960 + Math.random() * 200;
                planet.y = 20 + Math.random() * 480;
            }

            self.setPosition(planet.element, planet.x, planet.y);
        });
    };

    /* ====================================================================
       遊戲流程
       ==================================================================== */

    /** 開始新的一局 */
    Game.prototype.start = function () {
        this.clearEntities();

        this.fieldWidth = this.dom.field.clientWidth || 960;
        this.fieldHeight = this.dom.field.clientHeight || 534;

        this.state = 'playing';
        this.fuel = FUEL_START;
        this.score = 0;
        this.elapsed = 0;
        this.level = 1;
        this.spawnTimer = 0;
        this.fuelSpawnTimer = 1;
        this.enemyFireTimer = 1;
        this.canShoot = true;
        this.resetDirections();

        this.player.x = 60;
        this.player.y = (this.fieldHeight - this.player.height) / 2;
        this.dom.player.classList.remove('is-hit');
        this.setPosition(this.dom.player, this.player.x, this.player.y);

        this.dom.game.classList.remove('is-paused');
        this.dom.pauseOverlay.hidden = true;
        this.dom.gameOverOverlay.hidden = true;

        this.updateHud();
        this.sound.startBackground();
    };

    /** 切換暫停／繼續 */
    Game.prototype.togglePause = function () {
        if (this.state === 'playing') {
            this.state = 'paused';
            this.dom.game.classList.add('is-paused');
            this.dom.pauseOverlay.hidden = false;
            this.sound.pauseBackground();
            this.resetDirections();
        } else if (this.state === 'paused') {
            this.state = 'playing';
            this.dom.game.classList.remove('is-paused');
            this.dom.pauseOverlay.hidden = true;
            this.sound.resumeBackground();
        }

        return this.state === 'paused';
    };

    /** 遊戲結束：停止動畫、音效、互動與計時 */
    Game.prototype.gameOver = function () {
        this.state = 'over';
        this.fuel = 0;
        this.updateHud();
        this.resetDirections();

        this.dom.game.classList.add('is-paused');
        this.sound.stopBackground();

        this.dom.finalScore.textContent = String(this.score);
        this.dom.finalTime.textContent = String(Math.floor(this.elapsed));
        this.dom.gameOverOverlay.hidden = false;

        if (typeof this.onGameOver === 'function') {
            this.onGameOver({ score: this.score, time: Math.floor(this.elapsed) });
        }
    };

    /** 離開遊戲畫面時清乾淨 */
    Game.prototype.stop = function () {
        this.state = 'idle';
        this.clearEntities();
        this.sound.stopBackground();
        this.dom.game.classList.remove('is-paused');
        this.dom.pauseOverlay.hidden = true;
        this.dom.gameOverOverlay.hidden = true;
    };

    /* ====================================================================
       主迴圈
       ==================================================================== */

    /** 每一影格的更新 */
    Game.prototype.loop = function (timestamp) {
        var delta = this.lastFrameTime ? (timestamp - this.lastFrameTime) / 1000 : 0;
        this.lastFrameTime = timestamp;

        // 分頁切換回來時 delta 會很大，限制上限避免元素瞬移
        if (delta > 0.1) {
            delta = 0.1;
        }

        if (this.state !== 'paused' && this.state !== 'over') {
            this.updatePlanets(delta);
        }

        if (this.state === 'playing') {
            this.update(delta);
        }

        global.requestAnimationFrame(this.loop);
    };

    /** 遊戲進行中的更新 */
    Game.prototype.update = function (delta) {
        this.elapsed += delta;

        // 每 5 秒提高難度
        var level = Math.min(MAX_LEVEL, Math.floor(this.elapsed / LEVEL_DURATION) + 1);
        this.level = level;

        this.consumeFuel(delta);
        this.movePlayer(delta);
        this.spawnEntities(delta);
        this.moveEntities(delta);
        this.detectCollisions();
        this.updateHud();

        if (this.fuel <= 0) {
            this.gameOver();
        }
    };

    /** 燃料每秒減少 1 點 */
    Game.prototype.consumeFuel = function (delta) {
        this.fuel = Math.max(0, this.fuel - FUEL_PER_SECOND * delta);
    };

    /** 依感應區的啟動狀態移動主飛船，並限制在遊戲區域內 */
    Game.prototype.movePlayer = function (delta) {
        var distance = PLAYER_SPEED * delta;

        if (this.directions.up) {
            this.player.y -= distance;
        }
        if (this.directions.down) {
            this.player.y += distance;
        }
        if (this.directions.left) {
            this.player.x -= distance;
        }
        if (this.directions.right) {
            this.player.x += distance;
        }

        // 不可飛出遊戲畫面
        this.player.x = this.clamp(this.player.x, 0, this.fieldWidth - this.player.width);
        this.player.y = this.clamp(this.player.y, 0, this.fieldHeight - this.player.height);

        this.setPosition(this.dom.player, this.player.x, this.player.y);
    };

    /* ====================================================================
       元素的產生與移動
       ==================================================================== */

    /** 依難度定時產生敵方飛船、友方飛船、小行星與燃料 */
    Game.prototype.spawnEntities = function (delta) {
        var levelIndex = this.level - 1;
        var spawnInterval = Math.max(0.35, 1.5 - levelIndex * 0.12);
        var fuelInterval = Math.max(2.2, 4.5 - levelIndex * 0.25);
        var fireInterval = Math.max(0.45, 2.0 - levelIndex * 0.16);

        this.spawnTimer += delta;
        if (this.spawnTimer >= spawnInterval) {
            this.spawnTimer = 0;
            this.spawnFlyingObject();
        }

        this.fuelSpawnTimer += delta;
        if (this.fuelSpawnTimer >= fuelInterval) {
            this.fuelSpawnTimer = 0;
            this.spawnFuel();
        }

        this.enemyFireTimer += delta;
        if (this.enemyFireTimer >= fireInterval) {
            this.enemyFireTimer = 0;
            this.enemyShoot();
        }
    };

    /** 隨機產生一個敵方飛船／友方飛船／小行星，位置隨機、由右往左飛 */
    Game.prototype.spawnFlyingObject = function () {
        var roll = Math.random();
        var speedFactor = 1 + (this.level - 1) * 0.12;
        var entity;

        if (roll < 0.45) {
            entity = this.createEntity('tplEnemy', 'enemy', 58, 58);
        } else if (roll < 0.75) {
            entity = this.createEntity('tplAsteroid', 'asteroid', 56, 56);
            entity.hitPoints = 2; // 小行星需要兩發才會被摧毀
            entity.element.querySelector('.sprite__art').src = ASTEROID_IMAGES[Math.floor(Math.random() * ASTEROID_IMAGES.length)];
        } else {
            entity = this.createEntity('tplFriend', 'friend', 58, 58);
        }

        entity.x = this.fieldWidth + 20;
        entity.y = Math.random() * (this.fieldHeight - entity.height);
        entity.speedX = -(110 + Math.random() * 90) * speedFactor;
        // 少量上下擺動讓畫面更生動
        entity.speedY = (Math.random() - 0.5) * 40;

        this.pushEntity(entity);
    };

    /** 燃料圖示由畫面上方隨機水平位置落下 */
    Game.prototype.spawnFuel = function () {
        var entity = this.createEntity('tplFuel', 'fuel', 38, 38);

        entity.x = Math.random() * (this.fieldWidth - entity.width - 120) + 60;
        entity.y = -entity.height;
        entity.speedX = -20;
        entity.speedY = 90 + (this.level - 1) * 8;

        this.pushEntity(entity);
    };

    /** 主飛船射擊：一次按壓只發射一發 */
    Game.prototype.playerShoot = function () {
        if (this.state !== 'playing' || !this.canShoot) {
            return;
        }

        this.canShoot = false;

        var shot = this.createEntity('tplShot', 'playerShot', 18, 5);
        shot.x = this.player.x + this.player.width - 6;
        shot.y = this.player.y + this.player.height / 2 - 2;
        shot.speedX = PLAYER_SHOT_SPEED;
        shot.speedY = 0;

        this.pushEntity(shot);
        this.sound.playShoot();
    };

    /** 放開空白鍵後才能再次射擊 */
    Game.prototype.releaseShoot = function () {
        this.canShoot = true;
    };

    /** 敵方飛船射擊（由右往左） */
    Game.prototype.enemyShoot = function () {
        var enemies = this.entities.filter(function (entity) {
            return entity.kind === 'enemy';
        });

        if (enemies.length === 0) {
            return;
        }

        // 難度越高，同時開火的敵人越多
        var shooters = Math.min(enemies.length, 1 + Math.floor((this.level - 1) / 3));
        var self = this;

        for (var index = 0; index < shooters; index += 1) {
            var enemy = enemies[Math.floor(Math.random() * enemies.length)];
            var shot = self.createEntity('tplShot', 'enemyShot', 18, 5);

            shot.element.classList.add('shot--enemy');
            shot.x = enemy.x - 10;
            shot.y = enemy.y + enemy.height / 2 - 2;
            shot.speedX = -ENEMY_SHOT_SPEED;
            shot.speedY = 0;

            self.pushEntity(shot);
        }
    };

    /** 由樣板建立元素 */
    Game.prototype.createEntity = function (templateId, kind, width, height) {
        var template = document.getElementById(templateId);
        var element = template.content.firstElementChild.cloneNode(true);

        return {
            element: element,
            kind: kind,
            width: width,
            height: height,
            x: 0,
            y: 0,
            speedX: 0,
            speedY: 0,
            hitPoints: 1,
            dead: false
        };
    };

    /** 把元素加入場地 */
    Game.prototype.pushEntity = function (entity) {
        this.setPosition(entity.element, entity.x, entity.y);
        this.dom.field.appendChild(entity.element);
        this.entities.push(entity);
    };

    /** 移動所有元素並移除離開畫面的元素 */
    Game.prototype.moveEntities = function (delta) {
        var self = this;
        var alive = [];

        this.entities.forEach(function (entity) {
            entity.x += entity.speedX * delta;
            entity.y += entity.speedY * delta;

            var outside = entity.x + entity.width < -40
                || entity.x > self.fieldWidth + 60
                || entity.y > self.fieldHeight + 60
                || entity.y + entity.height < -80;

            if (entity.dead || outside) {
                self.removeElement(entity.element);
                return;
            }

            self.setPosition(entity.element, entity.x, entity.y);
            alive.push(entity);
        });

        this.entities = alive;
    };

    /* ====================================================================
       碰撞判定
       ==================================================================== */

    Game.prototype.detectCollisions = function () {
        this.detectPlayerShotHits();
        this.detectPlayerHits();
    };

    /** 主飛船子彈擊中目標：一發只能摧毀一個目標 */
    Game.prototype.detectPlayerShotHits = function () {
        var self = this;

        this.entities.forEach(function (shot) {
            if (shot.kind !== 'playerShot' || shot.dead) {
                return;
            }

            // 找出所有相交的目標，取最左邊（最先被打到）的那一個
            var target = null;

            self.entities.forEach(function (entity) {
                if (entity.dead || entity === shot) {
                    return;
                }
                if (entity.kind !== 'enemy' && entity.kind !== 'friend' && entity.kind !== 'asteroid') {
                    return;
                }
                if (!self.isOverlapping(shot, entity)) {
                    return;
                }
                if (target === null || entity.x < target.x) {
                    target = entity;
                }
            });

            if (target === null) {
                return;
            }

            // 子彈用掉了，不會穿透打到其他元素
            shot.dead = true;

            target.hitPoints -= 1;

            if (target.hitPoints > 0) {
                // 小行星第一次被擊中：顯示受損狀態
                target.element.classList.add('is-damaged');
                return;
            }

            self.destroyEntity(target, true);
        });
    };

    /** 主飛船與其他元素的碰撞 */
    Game.prototype.detectPlayerHits = function () {
        var self = this;
        var playerBox = {
            // 縮小一點判定範圍，讓操作手感更好
            x: this.player.x + 8,
            y: this.player.y + 8,
            width: this.player.width - 16,
            height: this.player.height - 16
        };

        this.entities.forEach(function (entity) {
            if (entity.dead || !self.isOverlapping(playerBox, entity)) {
                return;
            }

            if (entity.kind === 'fuel') {
                // 收集燃料
                entity.dead = true;
                self.addFuel(FUEL_PICKUP);
                self.showFloatingText('+' + FUEL_PICKUP + ' fuel', entity.x, entity.y, true);
                return;
            }

            if (entity.kind === 'enemyShot') {
                // 被敵方子彈擊中
                entity.dead = true;
                self.hitPlayer();
                return;
            }

            if (entity.kind === 'enemy' || entity.kind === 'friend' || entity.kind === 'asteroid') {
                // 撞上飛船或小行星：該元素被摧毀，燃料 -15
                self.destroyEntity(entity, false);
                self.hitPlayer();
            }
        });
    };

    /** 玩家受到傷害 */
    Game.prototype.hitPlayer = function () {
        this.addFuel(-FUEL_PENALTY);
        this.showFloatingText('-' + FUEL_PENALTY + ' fuel', this.player.x, this.player.y, false);

        this.dom.player.classList.remove('is-hit');
        // 重新觸發動畫
        void this.dom.player.offsetWidth;
        this.dom.player.classList.add('is-hit');
    };

    /**
     * 摧毀場上元素並結算分數
     * @param {Object} entity 被摧毀的元素
     * @param {boolean} byShot 是否為被子彈擊毀（撞擊不計分）
     */
    Game.prototype.destroyEntity = function (entity, byShot) {
        entity.dead = true;

        this.showExplosion(entity.x + entity.width / 2, entity.y + entity.height / 2);
        this.sound.playDestroy();

        if (!byShot) {
            return;
        }

        var points = 0;

        if (entity.kind === 'enemy') {
            points = SCORE_ENEMY;
        } else if (entity.kind === 'asteroid') {
            points = SCORE_ASTEROID;
        } else if (entity.kind === 'friend') {
            points = SCORE_FRIEND;
        }

        if (points !== 0) {
            this.score += points;   // 允許負分
            this.showFloatingText((points > 0 ? '+' : '') + points, entity.x, entity.y, points > 0);
        }
    };

    /** 兩個矩形是否重疊 */
    Game.prototype.isOverlapping = function (a, b) {
        return a.x < b.x + b.width
            && a.x + a.width > b.x
            && a.y < b.y + b.height
            && a.y + a.height > b.y;
    };

    /* ====================================================================
       畫面效果與資訊列
       ==================================================================== */

    /** 爆炸效果 */
    Game.prototype.showExplosion = function (x, y) {
        var template = document.getElementById('tplExplosion');
        var element = template.content.firstElementChild.cloneNode(true);
        var self = this;

        element.style.transform = 'translate3d(' + x + 'px,' + y + 'px,0)';
        this.dom.field.appendChild(element);

        global.setTimeout(function () {
            self.removeElement(element);
        }, 500);
    };

    /** 加分／扣分的浮動文字 */
    Game.prototype.showFloatingText = function (text, x, y, isGain) {
        var element = document.createElement('span');
        var self = this;

        element.className = 'floating-text ' + (isGain ? 'floating-text--gain' : 'floating-text--loss');
        element.textContent = text;
        element.style.transform = 'translate3d(' + x + 'px,' + y + 'px,0)';
        this.dom.field.appendChild(element);

        global.setTimeout(function () {
            self.removeElement(element);
        }, 950);
    };

    /** 增減燃料（上限 30 點），並觸發長條動畫 */
    Game.prototype.addFuel = function (amount) {
        this.fuel = this.clamp(this.fuel + amount, 0, FUEL_MAX);

        var bar = this.dom.fuelBar.parentNode;
        var className = amount >= 0 ? 'is-gaining' : 'is-losing';

        bar.classList.remove('is-gaining', 'is-losing');
        void bar.offsetWidth; // 重新觸發動畫
        bar.classList.add(className);

        this.updateHud();
    };

    /** 更新計時器、分數與燃料的顯示 */
    Game.prototype.updateHud = function () {
        var fuel = Math.max(0, Math.ceil(this.fuel));

        this.dom.timerValue.textContent = String(Math.floor(this.elapsed));
        this.dom.scoreValue.textContent = String(this.score);
        this.dom.fuelValue.textContent = String(fuel);
        this.dom.fuelBar.style.width = (fuel / FUEL_MAX * 100) + '%';

        this.dom.scoreCounter.classList.toggle('is-negative', this.score < 0);
        this.dom.fuelCounter.classList.toggle('is-low', fuel <= 5);
    };

    /* ====================================================================
       輔助方法
       ==================================================================== */

    /** 設定感應區方向的啟動狀態 */
    Game.prototype.setDirection = function (direction, isActive) {
        if (this.state !== 'playing') {
            return;
        }
        if (Object.prototype.hasOwnProperty.call(this.directions, direction)) {
            this.directions[direction] = isActive;
        }
    };

    /** 停止所有方向 */
    Game.prototype.resetDirections = function () {
        this.directions.up = false;
        this.directions.down = false;
        this.directions.left = false;
        this.directions.right = false;

        Array.prototype.forEach.call(this.dom.dpad.children, function (area) {
            area.classList.remove('is-active');
        });
    };

    /** 清空場上所有元素 */
    Game.prototype.clearEntities = function () {
        var self = this;

        this.entities.forEach(function (entity) {
            self.removeElement(entity.element);
        });
        this.entities = [];

        // 清掉殘留的爆炸與浮動文字
        Array.prototype.slice.call(this.dom.field.querySelectorAll('.explosion, .floating-text'))
            .forEach(function (element) {
                self.removeElement(element);
            });
    };

    /** 安全移除節點 */
    Game.prototype.removeElement = function (element) {
        if (element && element.parentNode) {
            element.parentNode.removeChild(element);
        }
    };

    /** 以 transform 設定位置（效能較佳） */
    Game.prototype.setPosition = function (element, x, y) {
        element.style.transform = 'translate3d(' + x.toFixed(1) + 'px,' + y.toFixed(1) + 'px,0)';
    };

    /** 限制數值範圍 */
    Game.prototype.clamp = function (value, min, max) {
        return Math.min(max, Math.max(min, value));
    };

    global.StarBattle = global.StarBattle || {};
    global.StarBattle.Game = Game;
}(window));
