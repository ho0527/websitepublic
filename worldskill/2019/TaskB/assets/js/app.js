/**
 * Kazan MuseumTour — 前台互動腳本
 *
 * 內容：
 *   1. 行動裝置主選單與 Museums 下拉選單
 *   2. 頁面切換：以 fetch 取得新頁面並帶淡入淡出動畫置換內容（不整頁重新載入）
 *   3. 聯絡表單：前端驗證 + 以 fetch 送往 Formspree，並顯示成功／失敗訊息
 *
 * 全部以事件委派（event delegation）綁定在 document 上，
 * 因此頁面內容被抽換之後不需要重新註冊事件。
 */
(function () {
    'use strict';

    var main = document.getElementById('primary');
    var announcer = document.getElementById('route-announcer');
    var progress = document.querySelector('.route-progress');
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ================================================================
       1. 選單
       ================================================================ */
    document.addEventListener('click', function (event) {
        var toggle = event.target.closest('.menu-toggle');
        if (toggle) {
            var nav = document.getElementById('site-nav');
            var open = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
            if (nav) {
                nav.classList.toggle('is-open', !open);
            }
            return;
        }

        var subToggle = event.target.closest('.submenu-toggle');
        if (subToggle) {
            var submenu = document.getElementById(subToggle.getAttribute('aria-controls'));
            var expanded = subToggle.getAttribute('aria-expanded') === 'true';
            subToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            if (submenu) {
                submenu.classList.toggle('is-open', !expanded);
            }
        }
    });

    // Esc 關閉展開中的選單
    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }
        document.querySelectorAll('[aria-expanded="true"]').forEach(function (element) {
            element.setAttribute('aria-expanded', 'false');
        });
        document.querySelectorAll('.is-open').forEach(function (element) {
            element.classList.remove('is-open');
        });
    });

    /* ================================================================
       2. 以 JavaScript 完成的頁面切換
       ================================================================ */
    var basePath = (function () {
        // <base> 由 script 標籤所在路徑推算：/worldskill/2019/TaskB/assets/js/app.js
        var script = document.currentScript || document.querySelector('script[src*="assets/js/app.js"]');
        if (!script) {
            return '/';
        }
        var src = script.getAttribute('src') || '';
        return src.replace(/assets\/js\/app\.js.*$/, '');
    })();

    /** 判斷這個連結是否應該由 JavaScript 接手 */
    function isInternalPageLink(link) {
        if (!link || link.target === '_blank' || link.hasAttribute('download')) {
            return false;
        }
        if (link.getAttribute('href') === null) {
            return false;
        }

        var url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin) {
            return false;
        }
        // 後台不套用（規格：page transition 不適用於管理後台）
        if (url.pathname.indexOf('/admin') !== -1) {
            return false;
        }
        // 站外或非本專案的路徑不接手
        if (basePath !== '/' && url.pathname.indexOf(basePath) !== 0) {
            return false;
        }
        // 檔案型網址（sitemap.xml、圖片…）交給瀏覽器
        if (/\.(xml|txt|jpe?g|png|gif|webp|svg|pdf|zip)$/i.test(url.pathname)) {
            return false;
        }
        // 純錨點
        if (url.pathname === window.location.pathname && url.hash && url.search === window.location.search) {
            return false;
        }
        return true;
    }

    function setProgress(value) {
        if (!progress) {
            return;
        }
        if (value === 0) {
            progress.style.transition = 'none';
            progress.style.width = '0';
            progress.style.opacity = '1';
            // 強制 reflow，讓接下來的寬度變化有動畫
            void progress.offsetWidth;
            progress.style.transition = '';
            return;
        }
        progress.style.width = value + '%';
        if (value >= 100) {
            window.setTimeout(function () {
                progress.style.opacity = '0';
                progress.style.width = '0';
            }, 250);
        }
    }

    /** 依新頁面的網址更新主選單的 aria-current */
    function refreshCurrentMenuItem(url) {
        document.querySelectorAll('.site-nav a').forEach(function (link) {
            if (link.href === url || link.href === url + '/') {
                link.setAttribute('aria-current', 'page');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    }

    /** 載入並置換頁面內容 */
    function loadPage(url, addToHistory) {
        if (!main) {
            window.location.href = url;
            return;
        }

        setProgress(0);
        setProgress(35);
        main.classList.add('is-leaving');

        var minimumDelay = reduceMotion ? 0 : 260;
        var startedAt = Date.now();

        fetch(url, { headers: { 'X-Requested-With': 'fetch' }, credentials: 'same-origin' })
            .then(function (response) {
                if (!response.ok && response.status !== 404) {
                    throw new Error('HTTP ' + response.status);
                }
                setProgress(70);
                return response.text();
            })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var newMain = doc.getElementById('primary');
                if (!newMain) {
                    throw new Error('No main content in response');
                }

                var wait = Math.max(0, minimumDelay - (Date.now() - startedAt));
                window.setTimeout(function () {
                    document.title = doc.title;
                    document.body.className = doc.body.className;
                    main.innerHTML = newMain.innerHTML;
                    main.setAttribute('data-page-title', newMain.getAttribute('data-page-title') || '');

                    // 重新播放進場動畫
                    main.classList.remove('is-leaving');
                    main.style.animation = 'none';
                    void main.offsetWidth;
                    main.style.animation = '';

                    if (addToHistory) {
                        window.history.pushState({ url: url }, '', url);
                    }

                    window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
                    refreshCurrentMenuItem(url);

                    // 關閉行動選單
                    var nav = document.getElementById('site-nav');
                    if (nav) {
                        nav.classList.remove('is-open');
                    }
                    var toggle = document.querySelector('.menu-toggle');
                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'false');
                    }

                    // 讓螢幕報讀器知道頁面已更換，並把焦點移到主要內容
                    if (announcer) {
                        announcer.textContent = 'Navigated to ' + document.title;
                    }
                    main.setAttribute('tabindex', '-1');
                    main.focus({ preventScroll: true });

                    setProgress(100);
                }, wait);
            })
            .catch(function () {
                // 任何錯誤都退回瀏覽器原生導覽，確保功能不中斷
                window.location.href = url;
            });
    }

    document.addEventListener('click', function (event) {
        if (event.defaultPrevented || event.button !== 0) {
            return;
        }
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        var link = event.target.closest('a');
        if (!isInternalPageLink(link)) {
            return;
        }

        event.preventDefault();
        loadPage(link.href, true);
    });

    window.addEventListener('popstate', function () {
        loadPage(window.location.href, false);
    });

    /* ================================================================
       3. 聯絡表單
       ================================================================ */
    function showFeedback(form, message, isSuccess) {
        var feedback = form.querySelector('[data-form-feedback]');
        if (!feedback) {
            return;
        }
        feedback.textContent = message;
        feedback.classList.add('is-visible');
        feedback.classList.toggle('form-feedback--success', isSuccess);
        feedback.classList.toggle('form-feedback--error', !isSuccess);
    }

    function setFieldError(form, name, message) {
        var field = form.querySelector('[name="' + name + '"]');
        var errorBox = form.querySelector('[data-error-for="' + name + '"]');
        if (field) {
            if (message) {
                field.setAttribute('aria-invalid', 'true');
            } else {
                field.removeAttribute('aria-invalid');
            }
        }
        if (errorBox) {
            errorBox.textContent = message || '';
            errorBox.classList.toggle('is-visible', Boolean(message));
        }
    }

    /** 前端驗證，回傳錯誤數量 */
    function validateContactForm(form) {
        var errors = 0;
        var name = form.elements.name;
        var email = form.elements.email;
        var content = form.elements.content;

        setFieldError(form, 'name', '');
        setFieldError(form, 'email', '');
        setFieldError(form, 'content', '');

        if (!name.value.trim()) {
            setFieldError(form, 'name', 'Please tell us your name.');
            errors++;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email.value.trim())) {
            setFieldError(form, 'email', 'Please enter a valid email address so we can reply.');
            errors++;
        }
        if (content.value.trim().length < 10) {
            setFieldError(form, 'content', 'Please write at least 10 characters.');
            errors++;
        }

        return errors;
    }

    document.addEventListener('submit', function (event) {
        var form = event.target.closest('[data-contact-form]');
        if (!form) {
            return;
        }

        event.preventDefault();

        if (validateContactForm(form) > 0) {
            showFeedback(form, 'Please correct the highlighted fields and try again.', false);
            var firstInvalid = form.querySelector('[aria-invalid="true"]');
            if (firstInvalid) {
                firstInvalid.focus();
            }
            return;
        }

        // Formspree 以 _replyto 作為回信地址
        var mirror = form.querySelector('[data-mirror="email"]');
        if (mirror) {
            mirror.value = form.elements.email.value.trim();
        }

        var button = form.querySelector('button[type="submit"]');
        if (button) {
            button.disabled = true;
            button.textContent = 'Sending…';
        }

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { Accept: 'application/json' }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                showFeedback(form, form.dataset.success, true);
                form.reset();
            })
            .catch(function () {
                showFeedback(form, form.dataset.error, false);
            })
            .then(function () {
                if (button) {
                    button.disabled = false;
                    button.textContent = 'Send message';
                }
            });
    });
})();
