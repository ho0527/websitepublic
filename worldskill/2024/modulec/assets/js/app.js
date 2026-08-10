/**
 * WorldSkills 2024 Web Technologies - 模組 C 前端互動
 *
 * 兩個功能：
 *   1. 封面圖片的聚光效果：徑向漸層遮罩的圓心跟著滑鼠移動。
 *   2. 內文照片的放大檢視：點擊放大，再次點擊或捲動即還原。
 *
 * 沒有使用任何外部函式庫，離線環境可直接運作。
 */

(function () {
    'use strict';

    /* ------------------------------------------------------------------
       1. 封面聚光效果
       ------------------------------------------------------------------ */

    /**
     * 讓封面的遮罩圓心跟隨滑鼠位置。
     * CSS 端以 --spotlight-x / --spotlight-y 兩個自訂屬性決定圓心。
     */
    function initCoverSpotlight() {
        var cover = document.querySelector('[data-spotlight]');
        if (!cover) {
            return;
        }

        var frame = cover.querySelector('.cover__frame') || cover;

        function moveSpotlight(event) {
            var bounds = frame.getBoundingClientRect();
            var x = event.clientX - bounds.left;
            var y = event.clientY - bounds.top;

            cover.style.setProperty('--spotlight-x', x.toFixed(1) + 'px');
            cover.style.setProperty('--spotlight-y', y.toFixed(1) + 'px');
        }

        // 滑鼠在整個視窗移動時都更新，離開封面範圍後聚光圈會自然移出畫面
        document.addEventListener('mousemove', moveSpotlight);

        // 觸控裝置沒有滑鼠指標，改成跟著手指
        frame.addEventListener('touchmove', function (event) {
            if (event.touches.length > 0) {
                moveSpotlight(event.touches[0]);
            }
        }, { passive: true });
    }

    /* ------------------------------------------------------------------
       2. 內文照片放大檢視
       ------------------------------------------------------------------ */

    function initPhotoLightbox() {
        var scope = document.querySelector('[data-lightbox-scope]');
        var lightbox = document.getElementById('lightbox');
        var lightboxImage = document.getElementById('lightbox-image');
        var closeButton = document.getElementById('lightbox-close');

        if (!scope || !lightbox || !lightboxImage) {
            return;
        }

        var isOpen = false;
        var lastFocused = null;

        /** 開啟放大檢視 */
        function openLightbox(image) {
            lightboxImage.setAttribute('src', image.getAttribute('src') || '');
            lightboxImage.setAttribute('alt', image.getAttribute('alt') || '');
            lightbox.hidden = false;
            isOpen = true;
            lastFocused = document.activeElement;

            if (closeButton) {
                closeButton.focus();
            }
        }

        /** 關閉放大檢視並回到原本的內容 */
        function closeLightbox() {
            if (!isOpen) {
                return;
            }

            lightbox.hidden = true;
            lightboxImage.setAttribute('src', '');
            isOpen = false;

            if (lastFocused && typeof lastFocused.focus === 'function') {
                lastFocused.focus();
            }
        }

        // 內文中的每一張照片都可以用滑鼠或鍵盤開啟
        var photos = scope.querySelectorAll('img');
        Array.prototype.forEach.call(photos, function (image) {
            image.setAttribute('tabindex', '0');
            image.setAttribute('role', 'button');
            image.setAttribute('aria-label', 'Enlarge photo: ' + (image.getAttribute('alt') || 'photo'));

            image.addEventListener('click', function () {
                openLightbox(image);
            });

            image.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ' || event.key === 'Spacebar') {
                    event.preventDefault();
                    openLightbox(image);
                }
            });
        });

        // 點擊放大後的照片（或遮罩任一處）即關閉
        lightbox.addEventListener('click', closeLightbox);

        // 放大期間捲動頁面也會關閉並回到內容
        window.addEventListener('scroll', function () {
            closeLightbox();
        }, { passive: true });

        window.addEventListener('wheel', function () {
            closeLightbox();
        }, { passive: true });

        window.addEventListener('touchmove', function () {
            closeLightbox();
        }, { passive: true });

        // 鍵盤使用者可用 Esc 關閉
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeLightbox();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initCoverSpotlight();
        initPhotoLightbox();
    });
}());
