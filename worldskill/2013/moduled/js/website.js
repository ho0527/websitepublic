/**
 * WorldSkills 2013 - Skill 17 Web Design / Module D
 * "Introducing Leipzig" 互動腳本
 *
 * 本檔案以原生 JavaScript 撰寫（不依賴 jQuery / MooTools，避免兩套函式庫互相干擾），
 * 並且在關閉 JavaScript 時，CSS 仍會以 keyframes 動畫輪播特色照片，
 * 確保 15 秒內所有照片皆可被看見。
 */
(function () {
    'use strict';

    /* ---------------------------------------------------------------------
     * 1. 特色照片輪播
     *    #feature-control-1 ~ 4：切換到指定照片
     *    #feature-control-5   ：播放 / 暫停切換
     * ------------------------------------------------------------------- */
    function initFeaturedSlider() {
        var wrapper = document.getElementById('featured');
        if (!wrapper) {
            return;
        }

        var slides = wrapper.querySelectorAll('ul > li');
        var dots = [];
        var i;

        for (i = 1; i <= slides.length; i++) {
            var dot = document.getElementById('feature-control-' + i);
            if (dot) {
                dots.push(dot);
            }
        }

        var toggle = document.getElementById('feature-control-' + (slides.length + 1));
        if (!slides.length || !dots.length) {
            return;
        }

        // 加上旗標讓 CSS 停用 keyframes 動畫，改由 class 控制
        document.documentElement.className += ' js-slider';

        var current = 0;
        var playing = true;
        var timer = null;
        var INTERVAL = 3500;   // 3.5 秒 × 4 張 = 14 秒，符合 15 秒內看完全部照片的要求

        function show(index) {
            current = (index + slides.length) % slides.length;
            for (var n = 0; n < slides.length; n++) {
                slides[n].className = (n === current) ? 'is-current' : '';
            }
            for (var d = 0; d < dots.length; d++) {
                dots[d].className = (d === current) ? 'is-active' : '';
            }
        }

        function next() {
            show(current + 1);
        }

        function play() {
            playing = true;
            if (timer) {
                window.clearInterval(timer);
            }
            timer = window.setInterval(next, INTERVAL);
            if (toggle) {
                toggle.className = '';
            }
        }

        function pause() {
            playing = false;
            window.clearInterval(timer);
            timer = null;
            if (toggle) {
                toggle.className = 'is-paused';
            }
        }

        // 點選數字圓鈕：切到對應照片並暫停自動播放，方便使用者仔細觀看
        for (i = 0; i < dots.length; i++) {
            (function (index) {
                dots[index].addEventListener('click', function () {
                    show(index);
                    pause();
                });
            }(i));
        }

        if (toggle) {
            toggle.addEventListener('click', function () {
                if (playing) {
                    pause();
                } else {
                    play();
                }
            });
        }

        // 滑鼠停在照片上時暫停，離開後恢復播放
        wrapper.addEventListener('mouseenter', function () {
            if (playing && timer) {
                window.clearInterval(timer);
                timer = null;
            }
        });

        wrapper.addEventListener('mouseleave', function () {
            if (playing && !timer) {
                timer = window.setInterval(next, INTERVAL);
            }
        });

        show(0);
        play();
    }

    /* ---------------------------------------------------------------------
     * 2. Leipzig Tweets 互動：點擊卡片展開／收合完整推文內容
     * ------------------------------------------------------------------- */
    function initTweets() {
        var items = document.querySelectorAll('.tweets li');
        if (!items.length) {
            return;
        }

        for (var i = 0; i < items.length; i++) {
            (function (item) {
                // 讓卡片可用鍵盤操作，兼顧無障礙
                item.setAttribute('tabindex', '0');
                item.setAttribute('role', 'button');

                function toggleOpen() {
                    var opened = item.className.indexOf('is-open') !== -1;
                    item.className = opened ? '' : 'is-open';
                }

                item.addEventListener('click', function (event) {
                    // 點在推文內的連結時不要觸發展開
                    if (event.target.tagName.toLowerCase() === 'a') {
                        return;
                    }
                    toggleOpen();
                });

                item.addEventListener('keydown', function (event) {
                    if (event.keyCode === 13 || event.keyCode === 32) {
                        event.preventDefault();
                        toggleOpen();
                    }
                });
            }(items[i]));
        }
    }

    /* ---------------------------------------------------------------------
     * 3. 表格列點擊時鎖定highlight，方便對照數據
     * ------------------------------------------------------------------- */
    function initTableRows() {
        var rows = document.querySelectorAll('#id-for-title-3 tbody tr');

        for (var i = 0; i < rows.length; i++) {
            (function (row) {
                row.addEventListener('click', function () {
                    var picked = row.getAttribute('data-picked') === '1';
                    for (var n = 0; n < rows.length; n++) {
                        rows[n].removeAttribute('data-picked');
                        rows[n].style.outline = '';
                    }
                    if (!picked) {
                        row.setAttribute('data-picked', '1');
                        row.style.outline = '2px solid #c19a4b';
                    }
                });
            }(rows[i]));
        }
    }

    /* ---------------------------------------------------------------------
     * 4. 影片：同一時間只播放一部，避免聲音互相干擾
     * ------------------------------------------------------------------- */
    function initVideos() {
        var videos = document.querySelectorAll('video');

        for (var i = 0; i < videos.length; i++) {
            (function (video) {
                video.addEventListener('play', function () {
                    for (var n = 0; n < videos.length; n++) {
                        if (videos[n] !== video) {
                            videos[n].pause();
                        }
                    }
                });
            }(videos[i]));
        }
    }

    /* ---------------------------------------------------------------------
     * 啟動
     * ------------------------------------------------------------------- */
    document.addEventListener('DOMContentLoaded', function () {
        initFeaturedSlider();
        initTweets();
        initTableRows();
        initVideos();
    });
}());
