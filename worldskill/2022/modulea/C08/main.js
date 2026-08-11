/**
 * C08 Firework
 * 頁面上隨機位置、隨機時間發射煙火，升空後爆炸成多彩粒子，
 * 粒子受重力影響下墜、帶有拖尾並逐漸淡出。
 *
 * 說明：本題以純 Canvas 2D 繪製（不使用 firework_sprites.png 精靈圖），
 * 這樣顏色與粒子數量可完全隨機，效果更接近 firework.gif。
 * 未引用任何外部資源或 JavaScript 函式庫。
 */
(() => {
    var canvas = document.getElementById('canvas');
    var ctx = canvas.getContext('2d');

    // 升空中的煙火與爆炸後的粒子
    var rockets = [];
    var sparks = [];
    // 重力加速度
    var GRAVITY = 0.045;
    // 距離下一次自動發射還需要幾個影格
    var nextLaunchDelay = 0;

    /**
     * 產生指定區間的隨機數
     * @param {number} min 最小值
     * @param {number} max 最大值
     * @returns {number} 隨機數
     */
    var random = (min, max) => min + Math.random() * (max - min);

    /**
     * 依視窗大小調整畫布尺寸
     */
    var resize = () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    };

    /**
     * 發射一枚煙火
     */
    var launchRocket = () => {
        var startX = random(canvas.width * 0.1, canvas.width * 0.9);
        // 爆炸高度落在畫面上半部
        var targetY = random(canvas.height * 0.1, canvas.height * 0.45);

        rockets.push({
            x: startX,
            y: canvas.height,
            // 稍微傾斜的上升軌跡
            vx: random(-0.8, 0.8),
            vy: random(-11, -8),
            targetY: targetY,
            hue: Math.floor(random(0, 360)),
            trail: []
        });
    };

    /**
     * 在指定位置產生爆炸粒子
     * @param {number} x 爆炸中心 X
     * @param {number} y 爆炸中心 Y
     * @param {number} hue 主色相
     */
    var explode = (x, y, hue) => {
        var count = Math.floor(random(60, 110));
        for (var i = 0; i < count; i += 1) {
            var angle = (Math.PI * 2 * i) / count + random(-0.05, 0.05);
            var speed = random(1.5, 6.5);

            sparks.push({
                x: x,
                y: y,
                vx: Math.cos(angle) * speed,
                vy: Math.sin(angle) * speed,
                // 以主色相為基準做小幅度變化，讓同一朵煙火色彩豐富
                hue: (hue + random(-40, 40) + 360) % 360,
                alpha: 1,
                fade: random(0.008, 0.02),
                trail: []
            });
        }
    };

    /**
     * 更新升空中的煙火
     */
    var updateRockets = () => {
        for (var i = rockets.length - 1; i >= 0; i -= 1) {
            var rocket = rockets[i];

            rocket.trail.push({x: rocket.x, y: rocket.y});
            if (rocket.trail.length > 6) {
                rocket.trail.shift();
            }

            rocket.x += rocket.vx;
            rocket.y += rocket.vy;
            rocket.vy += GRAVITY * 2;

            // 到達預定高度或上升力耗盡時爆炸
            if (rocket.y <= rocket.targetY || rocket.vy >= 0) {
                explode(rocket.x, rocket.y, rocket.hue);
                rockets.splice(i, 1);
            }
        }
    };

    /**
     * 更新爆炸粒子
     */
    var updateSparks = () => {
        for (var i = sparks.length - 1; i >= 0; i -= 1) {
            var spark = sparks[i];

            spark.trail.push({x: spark.x, y: spark.y});
            if (spark.trail.length > 4) {
                spark.trail.shift();
            }

            spark.x += spark.vx;
            spark.y += spark.vy;
            // 空氣阻力與重力
            spark.vx *= 0.985;
            spark.vy = spark.vy * 0.985 + GRAVITY;
            spark.alpha -= spark.fade;

            if (spark.alpha <= 0) {
                sparks.splice(i, 1);
            }
        }
    };

    /**
     * 繪製整個畫面
     */
    var draw = () => {
        // 以半透明黑色覆蓋，產生殘影拖尾
        ctx.globalCompositeOperation = 'destination-out';
        ctx.fillStyle = 'rgba(0, 0, 0, 0.18)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // 疊加模式讓煙火更明亮
        ctx.globalCompositeOperation = 'lighter';
        ctx.lineCap = 'round';

        rockets.forEach((rocket) => {
            var head = rocket.trail[0] || rocket;
            ctx.beginPath();
            ctx.strokeStyle = 'hsl(' + rocket.hue + ', 100%, 70%)';
            ctx.lineWidth = 2.5;
            ctx.moveTo(head.x, head.y);
            ctx.lineTo(rocket.x, rocket.y);
            ctx.stroke();
        });

        sparks.forEach((spark) => {
            var tail = spark.trail[0] || spark;
            ctx.beginPath();
            ctx.strokeStyle = 'hsla(' + spark.hue.toFixed(0) + ', 100%, 62%, ' + spark.alpha.toFixed(3) + ')';
            ctx.lineWidth = 2;
            ctx.moveTo(tail.x, tail.y);
            ctx.lineTo(spark.x, spark.y);
            ctx.stroke();
        });
    };

    /**
     * 動畫主迴圈
     */
    var loop = () => {
        nextLaunchDelay -= 1;
        if (nextLaunchDelay <= 0) {
            launchRocket();
            // 隨機間隔（約 0.4 ~ 1.6 秒）再發射下一枚
            nextLaunchDelay = Math.floor(random(25, 95));
        }

        updateRockets();
        updateSparks();
        draw();
        window.requestAnimationFrame(loop);
    };

    window.addEventListener('resize', resize);
    // 點擊畫面可額外發射一枚煙火
    canvas.addEventListener('click', launchRocket);

    resize();
    loop();
})();
