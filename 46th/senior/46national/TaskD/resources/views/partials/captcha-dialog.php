<?php
/**
 * 問答驗證碼作答視窗。
 *
 * 這是覆蓋在訂票頁上方的區塊，不會另開新頁面；
 * 題目與圖片由 JavaScript 以 AJAX 取得後填入。
 *
 * @var \App\Core\View $view
 */
?>
<div class="captcha-overlay" id="captcha-overlay" role="dialog" aria-modal="true" aria-labelledby="captcha-question">
    <div class="captcha-dialog">
        <div class="captcha-header" id="captcha-question">載入題目中…</div>

        <div class="captcha-body">
            <!-- 點擊圖片會在游標位置畫出標記矩形 -->
            <div class="captcha-canvas" id="captcha-canvas">
                <img src="" alt="驗證碼題目圖片" id="captcha-image">
            </div>
        </div>

        <div class="captcha-footer">
            <button type="button" class="captcha-link" id="captcha-refresh">↻ 產生新的驗證問題</button>
            <span class="captcha-message" id="captcha-message"></span>
            <span>
                <button type="button" class="button button-secondary button-small" id="captcha-close">關閉</button>
                <button type="button" class="button" id="captcha-verify">驗證</button>
            </span>
        </div>
    </div>
</div>
