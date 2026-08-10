<?php
/**
 * 佈景初始化。
 *
 * 這段程式必須在頁面繪製前執行，否則會先閃一下預設佈景才切換過去，
 * 因此直接內嵌在 <head>，而不是等外部 JS 載入。
 */
?>
<script>
    (function () {
        try {
            var saved = window.localStorage.getItem('railBookingTheme');

            // 沒有存過偏好時採用預設的深色佈景
            if (saved === 'light') {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        } catch (error) {
            // 無法存取 localStorage（例如隱私模式）時維持預設佈景即可
        }
    })();
</script>
