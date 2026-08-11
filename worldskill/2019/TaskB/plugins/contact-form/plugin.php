<?php
/**
 * 外掛：Static Contact Form
 *
 * 依規格採用免費的靜態表單轉寄服務（Formspree）：
 *   <form action="https://formspree.io/admin@example.com" method="POST">
 * 欄位為 name、email（Formspree 慣用的 _replyto）與 content。
 *
 * 使用者回饋：
 *   - 有 JavaScript 時以 fetch 送出，成功／失敗都在頁面上顯示訊息，不會離開頁面
 *   - 沒有 JavaScript 時退回瀏覽器原生送出，並以 _next 導回本頁顯示成功訊息
 *   成功與錯誤文字都可在後台「Settings → Contact form」修改。
 */

declare(strict_types=1);

use App\Core\App;
use App\Core\Html;
use App\Core\PluginManager;
use App\Core\Url;

return static function (PluginManager $hooks, App $app): void {
    $hooks->addAction('contact_form', static function (array $args) use ($app): void {
        $action      = (string) ($args['action'] ?? '');
        $successText = (string) ($args['successText'] ?? '');
        $errorText   = (string) ($args['errorText'] ?? '');

        // 沒有 JavaScript 時，Formspree 會把使用者導回這個網址
        $returnUrl = Url::current('contact') . '?sent=1';
        $submitted = isset($_GET['sent']);
        ?>
        <form class="contact-form"
              action="<?= Html::e($action) ?>"
              method="POST"
              novalidate
              data-contact-form
              data-success="<?= Html::e($successText) ?>"
              data-error="<?= Html::e($errorText) ?>">

            <h2 class="contact-form__title">Send us a message</h2>

            <?php // 表單回饋區：aria-live 讓螢幕報讀器也能得知結果 ?>
            <p class="form-feedback<?= $submitted ? ' form-feedback--success is-visible' : '' ?>"
               role="status" aria-live="polite" data-form-feedback>
                <?= $submitted ? Html::e($successText) : '' ?>
            </p>

            <div class="form-row">
                <label class="form-label" for="contact-name">Your name <span aria-hidden="true">*</span></label>
                <input class="form-input" type="text" id="contact-name" name="name"
                       required autocomplete="name" aria-describedby="contact-name-error">
                <span class="form-error" id="contact-name-error" data-error-for="name"></span>
            </div>

            <div class="form-row">
                <label class="form-label" for="contact-email">Email address <span aria-hidden="true">*</span></label>
                <?php // Formspree 以 _replyto 作為回覆信箱；同時保留 email 名稱供其他服務使用 ?>
                <input class="form-input" type="email" id="contact-email" name="email"
                       required autocomplete="email" aria-describedby="contact-email-error">
                <span class="form-error" id="contact-email-error" data-error-for="email"></span>
            </div>

            <div class="form-row">
                <label class="form-label" for="contact-content">Message <span aria-hidden="true">*</span></label>
                <textarea class="form-input form-input--textarea" id="contact-content" name="content"
                          rows="6" required aria-describedby="contact-content-error"></textarea>
                <span class="form-error" id="contact-content-error" data-error-for="content"></span>
            </div>

            <?php // Formspree 設定欄位 ?>
            <input type="hidden" name="_replyto" value="" data-mirror="email">
            <input type="hidden" name="_subject" value="New message from <?= Html::e($app->setting('site_title')) ?>">
            <input type="hidden" name="_next" value="<?= Html::e($returnUrl) ?>">
            <?php // 蜜罐欄位：真人看不到，機器人填了就擋下 ?>
            <input type="text" name="_gotcha" class="screen-reader-text" tabindex="-1" autocomplete="off" aria-hidden="true">

            <p class="form-actions">
                <button class="button button--primary" type="submit">Send message</button>
                <span class="form-note">Fields marked * are required.</span>
            </p>
        </form>
        <?php
    });
};
