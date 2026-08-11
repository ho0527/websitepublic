<?php
/**
 * 聯絡我們頁
 *
 * 表單本身由「Static Contact Form」外掛透過 contact_form 掛鉤輸出，
 * 送出目標（Formspree 網址）、提示與錯誤文字都可在後台 Settings 修改。
 *
 * @var \App\Core\App $app
 * @var string        $formAction
 * @var string        $introText
 * @var string        $successText
 * @var string        $errorText
 * @var string        $contactMail
 */

use App\Core\Html;
use App\Core\Url;
?>
<section class="page-intro">
    <h1 class="page-intro__title">Contact us</h1>
    <p class="page-intro__lead"><?= Html::e($introText) ?></p>
</section>

<div class="contact-layout">
    <div class="contact-form-wrap">
        <?php // 外掛掛鉤：輸出靜態聯絡表單 ?>
        <?php $app->hooks->doAction('contact_form', [
            'action'      => $formAction,
            'successText' => $successText,
            'errorText'   => $errorText,
        ]); ?>
    </div>

    <aside class="contact-aside" aria-label="Other ways to reach us">
        <div class="info-card">
            <h2 class="info-card__title">Visitor centre</h2>
            <p>Kremlyovskaya St 2, Kazan, Republic of Tatarstan</p>
            <p>Open daily 09:00 - 19:00</p>
        </div>
        <div class="info-card">
            <h2 class="info-card__title">Write to us</h2>
            <p>
                <a href="mailto:<?= Html::e($contactMail) ?>"><?= Html::e($contactMail) ?></a>
            </p>
            <p>We answer within one working day.</p>
        </div>
        <div class="info-card">
            <h2 class="info-card__title">Group visits</h2>
            <p>Groups of ten or more should book at least three days in advance.</p>
            <p><a href="<?= Html::e(Url::to('museums')) ?>">Choose a museum first</a></p>
        </div>
    </aside>
</div>
