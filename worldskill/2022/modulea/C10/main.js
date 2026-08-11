/**
 * C10 Text Ellipsis
 * 容器縮放時文字永遠維持單行，寬度不足時於「文字中間」插入省略號，
 * 前後兩段文字都保留；容器變寬時會還原成完整文字。
 *
 * 純 CSS 的 text-overflow 只能在尾端省略，因此改以 JS 量測 + 二分搜尋處理。
 * 未引用任何函式庫或外部資源。
 */
(() => {
    var ELLIPSIS = '...';

    var container = document.querySelector('.container');
    var wrap = document.querySelector('.wrap');
    var txt = document.querySelector('.txt');

    if (!container || !wrap || !txt) {
        return;
    }

    // 保留原始文字，容器變寬時才能還原
    var fullText = txt.textContent;

    // 建立離畫面的量測用元素，字體樣式與原文字相同
    var measurer = document.createElement('span');
    measurer.style.position = 'absolute';
    measurer.style.left = '-99999px';
    measurer.style.top = '0';
    measurer.style.whiteSpace = 'pre';
    measurer.style.visibility = 'hidden';
    document.body.appendChild(measurer);

    /**
     * 同步量測元素的字型設定，確保寬度量測正確
     */
    var syncFont = () => {
        var style = window.getComputedStyle(txt);
        measurer.style.font = style.font;
        measurer.style.fontFamily = style.fontFamily;
        measurer.style.fontSize = style.fontSize;
        measurer.style.fontWeight = style.fontWeight;
        measurer.style.fontStyle = style.fontStyle;
        measurer.style.letterSpacing = style.letterSpacing;
    };

    /**
     * 量測一段文字的像素寬度
     * @param {string} text 待量測文字
     * @returns {number} 寬度（px）
     */
    var measure = (text) => {
        measurer.textContent = text;
        return measurer.getBoundingClientRect().width;
    };

    /**
     * 依保留的字元數組出「前段 + 省略號 + 後段」的字串
     * @param {number} keep 前後兩段合計保留的字元數
     * @returns {string} 組合後的字串
     */
    var buildText = (keep) => {
        // 前段多留一個字，讀起來較自然
        var front = Math.ceil(keep / 2);
        var back = keep - front;
        var head = fullText.slice(0, front);
        var tail = back > 0 ? fullText.slice(fullText.length - back) : '';
        return head + ELLIPSIS + tail;
    };

    /**
     * 依目前容器寬度重新計算要顯示的文字
     */
    var update = () => {
        syncFont();

        // 可用寬度以 .wrap 的內容寬度為準，並留 1px 緩衝避免四捨五入誤差
        var available = wrap.clientWidth - 1;
        if (available <= 0) {
            return;
        }

        // 完整文字放得下就直接顯示原文
        if (measure(fullText) <= available) {
            txt.textContent = fullText;
            return;
        }

        // 二分搜尋：找出在可用寬度內能保留的最大字元數
        var low = 0;
        var high = fullText.length;
        var best = 0;

        while (low <= high) {
            var mid = Math.floor((low + high) / 2);
            if (measure(buildText(mid)) <= available) {
                best = mid;
                low = mid + 1;
            } else {
                high = mid - 1;
            }
        }

        txt.textContent = buildText(best);
    };

    // 容器可用滑鼠拖曳縮放（resize: horizontal），以 ResizeObserver 即時反應
    if (typeof ResizeObserver === 'function') {
        var observer = new ResizeObserver(() => update());
        observer.observe(container);
    }

    // 視窗尺寸變化時也重新計算，並作為不支援 ResizeObserver 時的備援
    window.addEventListener('resize', update);
    window.addEventListener('load', update);

    update();
})();
