/**
 * C07 Rainbow Trail
 * 滑鼠在 #canvas 上移動時，於游標周圍產生多顆彩色小球，
 * 小球會向外擴散、逐漸放大並淡出，形成彩虹拖尾效果。
 * 全部使用原生 Canvas 2D API，未引用任何函式庫或外部資源。
 */
(() => {
    var canvas = document.getElementById('canvas');
    var ctx = canvas.getContext('2d');

    // 每次滑鼠移動要生成的小球數量（至少四種顏色）
    var BALLS_PER_MOVE = 5;
    // 小球集合
    var balls = [];
    // 彩虹色相起點，讓連續移動時顏色不斷變化
    var hueSeed = 0;

    /**
     * 產生指定區間的隨機數
     * @param {number} min 最小值
     * @param {number} max 最大值
     * @returns {number} 隨機數
     */
    var random = (min, max) => min + Math.random() * (max - min);

    /**
     * 在指定座標周圍產生一組彩色小球
     * @param {number} x 游標 X 座標
     * @param {number} y 游標 Y 座標
     */
    var spawnBalls = (x, y) => {
        for (var i = 0; i < BALLS_PER_MOVE; i += 1) {
            // 以隨機角度往外擴散
            var angle = random(0, Math.PI * 2);
            var speed = random(0.4, 2.4);

            balls.push({
                // 出生點在游標附近做小幅度偏移
                x: x + random(-8, 8),
                y: y + random(-8, 8),
                vx: Math.cos(angle) * speed,
                vy: Math.sin(angle) * speed,
                radius: random(4, 16),
                // 每顆球從彩虹色盤中取色，確保四色以上
                hue: (hueSeed + random(0, 90)) % 360,
                alpha: 1,
                // 淡出速度，數值越大消失越快
                fade: random(0.008, 0.02)
            });
            hueSeed = (hueSeed + 7) % 360;
        }
    };

    /**
     * 更新所有小球狀態，移除已完全透明的小球
     */
    var updateBalls = () => {
        for (var i = balls.length - 1; i >= 0; i -= 1) {
            var ball = balls[i];
            ball.x += ball.vx;
            ball.y += ball.vy;
            // 擴散過程速度逐漸衰減，並讓球體略為變大
            ball.vx *= 0.97;
            ball.vy *= 0.97;
            ball.radius += 0.25;
            ball.alpha -= ball.fade;

            if (ball.alpha <= 0) {
                balls.splice(i, 1);
            }
        }
    };

    /**
     * 繪製目前畫面
     */
    var drawBalls = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        balls.forEach((ball) => {
            ctx.beginPath();
            ctx.fillStyle = 'hsla(' + ball.hue.toFixed(0) + ', 85%, 55%, ' + ball.alpha.toFixed(3) + ')';
            ctx.arc(ball.x, ball.y, ball.radius, 0, Math.PI * 2);
            ctx.fill();
        });
    };

    /**
     * 動畫主迴圈
     */
    var loop = () => {
        updateBalls();
        drawBalls();
        window.requestAnimationFrame(loop);
    };

    canvas.addEventListener('mousemove', (event) => {
        // 將視窗座標換算成 canvas 內部座標
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        spawnBalls((event.clientX - rect.left) * scaleX, (event.clientY - rect.top) * scaleY);
    });

    loop();
})();
