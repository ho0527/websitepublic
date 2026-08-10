/**
 * 問答驗證碼。
 *
 * 作答視窗直接覆蓋在訂票頁面上，不會另開新頁面。
 * 點擊圖片時會以游標為中心畫出一個紅色矩形（框線 5px）標示選取的區塊，
 * 換題時會清除目前的選取，驗證則把選取座標送回伺服器比對。
 */
(function () {
    'use strict';

    var config = window.railBookingConfig || {};

    var overlay      = document.getElementById('captcha-overlay');
    var canvas       = document.getElementById('captcha-canvas');
    var image        = document.getElementById('captcha-image');
    var questionText = document.getElementById('captcha-question');
    var message      = document.getElementById('captcha-message');
    var stateLabel   = document.getElementById('captcha-state');
    var openButton   = document.getElementById('captcha-open');
    var closeButton  = document.getElementById('captcha-close');
    var refreshButton = document.getElementById('captcha-refresh');
    var verifyButton = document.getElementById('captcha-verify');

    if (!overlay || !canvas || !image || !openButton) {
        return;
    }

    /** 目前選取的座標，皆以圖片的原始尺寸為準 */
    var selections = [];

    /** 目前題目的標記矩形尺寸設定 */
    var markerSize = { width: 120, height: 100, border: 5 };

    /**
     * 送出 JSON 請求。
     */
    function request(url, method, body) {
        return fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: body ? JSON.stringify(body) : undefined
        }).then(function (response) {
            return response.json();
        });
    }

    /**
     * 清除畫面上所有的標記矩形與選取紀錄。
     */
    function clearSelections() {
        selections = [];

        var markers = canvas.querySelectorAll('.captcha-marker');

        for (var index = 0; index < markers.length; index++) {
            markers[index].remove();
        }
    }

    /**
     * 以百分比定位把標記矩形疊在圖片上，這樣縮放時位置仍然正確。
     */
    function drawMarker(naturalX, naturalY) {
        var naturalWidth  = image.naturalWidth || 1;
        var naturalHeight = image.naturalHeight || 1;

        var marker = document.createElement('div');
        marker.className = 'captcha-marker';
        // 以點擊時的游標為中心
        marker.style.left        = ((naturalX - markerSize.width / 2) / naturalWidth * 100) + '%';
        marker.style.top         = ((naturalY - markerSize.height / 2) / naturalHeight * 100) + '%';
        marker.style.width       = (markerSize.width / naturalWidth * 100) + '%';
        marker.style.height      = (markerSize.height / naturalHeight * 100) + '%';
        marker.style.borderWidth = markerSize.border + 'px';

        canvas.appendChild(marker);
    }

    /**
     * 套用一道新題目。
     */
    function applyQuestion(question) {
        if (!question) {
            questionText.textContent = '目前沒有可用的驗證題目';

            return;
        }

        questionText.textContent = question.text;
        image.src                = question.image;
        markerSize = {
            width:  question.marker_width,
            height: question.marker_height,
            border: question.marker_border
        };

        clearSelections();
    }

    /**
     * 更新訂票表單上的驗證狀態文字。
     */
    function setPassed(passed) {
        if (!stateLabel) {
            return;
        }

        stateLabel.textContent = passed ? '已通過驗證' : '尚未通過驗證';
        stateLabel.className   = 'captcha-state ' + (passed ? 'is-passed' : 'is-pending');
    }

    /**
     * 顯示提示訊息。
     */
    function setMessage(text, kind) {
        message.textContent = text || '';
        message.className   = 'captcha-message' + (kind ? ' is-' + kind : '');
    }

    /**
     * 載入題目並開啟作答視窗。
     */
    openButton.addEventListener('click', function () {
        setMessage('');
        overlay.classList.add('is-open');

        request(config.captchaShowUrl, 'GET').then(function (data) {
            applyQuestion(data.question);
        });
    });

    closeButton.addEventListener('click', function () {
        overlay.classList.remove('is-open');
    });

    // 點擊視窗外圍也可關閉
    overlay.addEventListener('click', function (event) {
        if (event.target === overlay) {
            overlay.classList.remove('is-open');
        }
    });

    /**
     * 點擊圖片：以游標為中心畫出標記矩形。
     */
    canvas.addEventListener('click', function (event) {
        var bounds = image.getBoundingClientRect();

        if (bounds.width === 0 || bounds.height === 0) {
            return;
        }

        // 把畫面座標換算回圖片的原始座標
        var naturalX = Math.round((event.clientX - bounds.left) / bounds.width * (image.naturalWidth || 1));
        var naturalY = Math.round((event.clientY - bounds.top) / bounds.height * (image.naturalHeight || 1));

        selections.push({ x: naturalX, y: naturalY });
        drawMarker(naturalX, naturalY);
        setMessage('');
    });

    /**
     * 產生新的驗證問題：換題並清除目前的選取。
     */
    refreshButton.addEventListener('click', function () {
        setMessage('');

        request(config.captchaRefreshUrl, 'POST', {}).then(function (data) {
            applyQuestion(data.question);
            setPassed(false);
        });
    });

    /**
     * 驗證選取的區塊是否與題目相符。
     */
    verifyButton.addEventListener('click', function () {
        request(config.captchaVerifyUrl, 'POST', { selections: selections }).then(function (data) {
            if (data.success) {
                setPassed(true);
                setMessage(data.message, 'success');
                // 通過後稍待片刻再關閉，讓使用者看得到結果
                window.setTimeout(function () {
                    overlay.classList.remove('is-open');
                }, 700);

                return;
            }

            // 驗證失敗會自動換一道新題目
            setPassed(false);
            setMessage(data.message, 'error');
            applyQuestion(data.question);
        });
    });
})();
