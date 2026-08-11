/**
 * C09 Particle Clock
 * 以點陣粒子組成 HH:MM:SS 時鐘；當某個數字改變時，
 * 舊數字的粒子會炸開成彩色粒子往外飛散、受重力下墜並淡出，
 * 新數字的粒子則以縮放淡入的方式就定位。
 *
 * 點陣資料使用 index.html 中提供的全域常數 NUMBERS（0-9 與 ":"）。
 * 全部使用原生 Canvas 2D API，未引用任何函式庫或外部資源。
 */
(() => {
    // 建立畫布並鋪滿視窗
    var canvas = document.createElement('canvas');
    canvas.id = 'canvas';
    canvas.style.display = 'block';
    document.body.style.margin = '0';
    document.body.style.background = '#fff';
    document.body.style.overflow = 'hidden';
    document.body.appendChild(canvas);

    var ctx = canvas.getContext('2d');

    // 點陣參數：每個字元 7 欄 x 10 列
    var COLS = 7;
    var ROWS = 10;
    // 每個點所佔的格子邊長與實際半徑
    var UNIT = 14;
    var DOT_RADIUS = 5;
    // 字元之間的間距
    var CHAR_GAP = 12;
    // 冒號索引（NUMBERS 陣列的最後一項）
    var COLON_INDEX = 10;
    // 重力加速度
    var GRAVITY = 0.16;

    // 每個字元的狀態：目前字元與淡入進度
    var chars = [];
    // 爆散中的彩色粒子
    var particles = [];

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
     * 取得目前時間字串 HH:MM:SS
     * @returns {string} 時間字串
     */
    var getTimeText = () => {
        var now = new Date();
        var pad = (value) => (value < 10 ? '0' + value : '' + value);
        return pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
    };

    /**
     * 將字元轉成 NUMBERS 的索引
     * @param {string} char 單一字元
     * @returns {number} NUMBERS 索引
     */
    var charToIndex = (char) => (char === ':' ? COLON_INDEX : Number(char));

    /**
     * 計算整個時鐘左上角的起始座標
     * @param {number} length 字元數
     * @returns {{x: number, y: number}} 起始座標
     */
    var getOrigin = (length) => {
        var charWidth = COLS * UNIT;
        var totalWidth = length * charWidth + (length - 1) * CHAR_GAP;
        return {
            x: (canvas.width - totalWidth) / 2,
            y: (canvas.height - ROWS * UNIT) / 2 - 40
        };
    };

    /**
     * 取得某個字元所有亮點的畫布座標
     * @param {number} slot 字元在時鐘中的位置
     * @param {string} char 該位置的字元
     * @param {number} length 字元總數
     * @returns {{x: number, y: number}[]} 亮點座標陣列
     */
    var getDots = (slot, char, length) => {
        var origin = getOrigin(length);
        var charWidth = COLS * UNIT;
        var offsetX = origin.x + slot * (charWidth + CHAR_GAP);
        var matrix = NUMBERS[charToIndex(char)];
        var dots = [];

        if (!matrix) {
            return dots;
        }

        for (var row = 0; row < ROWS; row += 1) {
            for (var col = 0; col < COLS; col += 1) {
                if (matrix[row][col] === 1) {
                    dots.push({
                        x: offsetX + col * UNIT + UNIT / 2,
                        y: origin.y + row * UNIT + UNIT / 2
                    });
                }
            }
        }
        return dots;
    };

    /**
     * 讓舊數字的每個亮點炸開成彩色粒子
     * @param {{x: number, y: number}[]} dots 舊數字的亮點座標
     */
    var explodeDots = (dots) => {
        dots.forEach((dot) => {
            var angle = random(0, Math.PI * 2);
            var speed = random(1, 4);

            particles.push({
                x: dot.x,
                y: dot.y,
                vx: Math.cos(angle) * speed,
                vy: Math.sin(angle) * speed - random(0, 2),
                radius: DOT_RADIUS,
                // 隨機彩色
                hue: Math.floor(random(0, 360)),
                alpha: 1,
                fade: random(0.008, 0.018)
            });
        });
    };

    /**
     * 依目前時間更新字元狀態，並在有變化時觸發粒子動畫
     */
    var syncTime = () => {
        var text = getTimeText();

        for (var i = 0; i < text.length; i += 1) {
            var char = text.charAt(i);

            if (!chars[i]) {
                // 首次建立時直接顯示，不做動畫
                chars[i] = {char: char, progress: 1};
                continue;
            }

            if (chars[i].char !== char) {
                // 舊數字炸開，新數字重新淡入
                explodeDots(getDots(i, chars[i].char, text.length));
                chars[i].char = char;
                chars[i].progress = 0;
            }
        }
    };

    /**
     * 更新爆散粒子狀態
     */
    var updateParticles = () => {
        for (var i = particles.length - 1; i >= 0; i -= 1) {
            var particle = particles[i];
            particle.x += particle.vx;
            particle.y += particle.vy;
            particle.vx *= 0.99;
            particle.vy += GRAVITY;
            particle.alpha -= particle.fade;
            particle.radius *= 0.995;

            if (particle.alpha <= 0 || particle.y - particle.radius > canvas.height) {
                particles.splice(i, 1);
            }
        }
    };

    /**
     * 繪製時鐘本體（黑色點陣）與爆散粒子
     */
    var draw = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        var length = chars.length;

        chars.forEach((item, slot) => {
            // 淡入進度，讓新數字由小變大浮現
            item.progress = Math.min(1, item.progress + 0.08);
            var scale = 0.2 + 0.8 * item.progress;

            ctx.fillStyle = 'rgba(17, 17, 17, ' + item.progress.toFixed(3) + ')';
            getDots(slot, item.char, length).forEach((dot) => {
                ctx.beginPath();
                ctx.arc(dot.x, dot.y, DOT_RADIUS * scale, 0, Math.PI * 2);
                ctx.fill();
            });
        });

        particles.forEach((particle) => {
            ctx.beginPath();
            ctx.fillStyle = 'hsla(' + particle.hue + ', 90%, 55%, ' + particle.alpha.toFixed(3) + ')';
            ctx.arc(particle.x, particle.y, Math.max(0, particle.radius), 0, Math.PI * 2);
            ctx.fill();
        });
    };

    /**
     * 動畫主迴圈
     */
    var loop = () => {
        syncTime();
        updateParticles();
        draw();
        window.requestAnimationFrame(loop);
    };

    window.addEventListener('resize', resize);

    resize();
    loop();
})();
