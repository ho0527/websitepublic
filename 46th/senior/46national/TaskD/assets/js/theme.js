/**
 * 深色／淺色佈景切換。
 *
 * 佈景以 <html> 的 data-theme 屬性表示（預設深色、light 為淺色），
 * 使用者的選擇存在 localStorage，重新整理或換頁後仍會保留。
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'railBookingTheme';
    var button      = document.getElementById('theme-toggle');

    if (!button) {
        return;
    }

    /**
     * 取得目前的佈景。
     */
    function currentTheme() {
        return document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
    }

    /**
     * 套用佈景並記住使用者的選擇。
     */
    function applyTheme(theme) {
        if (theme === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
        } else {
            document.documentElement.removeAttribute('data-theme');
        }

        button.setAttribute('aria-label', theme === 'light' ? '切換為深色模式' : '切換為淺色模式');
        button.setAttribute('title', theme === 'light' ? '切換為深色模式' : '切換為淺色模式');

        try {
            window.localStorage.setItem(STORAGE_KEY, theme);
        } catch (error) {
            // 無法寫入 localStorage 時仍可切換，只是不會被記住
        }
    }

    button.addEventListener('click', function () {
        applyTheme(currentTheme() === 'light' ? 'dark' : 'light');
    });

    // 進入頁面時先同步一次按鈕的說明文字
    applyTheme(currentTheme());
})();
