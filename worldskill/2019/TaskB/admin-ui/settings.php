<?php
/**
 * 網站設定
 *
 * @var array $values 目前所有設定
 * @var array $images 媒體庫圖片
 */

use App\Core\Csrf;
use App\Core\Html;
use App\Core\Url;

$get = static fn (string $key): string => Html::e((string) ($values[$key] ?? ''));
?>
<form method="post" class="settings-form">
    <?= Csrf::field() ?>

    <section class="panel">
        <h2 class="panel__title">Site identity</h2>

        <div class="form-row">
            <label class="form-label" for="site_title">Heading title</label>
            <input class="form-input" type="text" id="site_title" name="site_title" value="<?= $get('site_title') ?>">
        </div>

        <div class="form-row">
            <label class="form-label" for="site_tagline">Tagline slogan</label>
            <input class="form-input" type="text" id="site_tagline" name="site_tagline" value="<?= $get('site_tagline') ?>">
        </div>

        <div class="form-row">
            <label class="form-label" for="site_description">Site description (used for SEO)</label>
            <textarea class="form-input form-input--textarea" id="site_description" name="site_description" rows="3"><?= $get('site_description') ?></textarea>
        </div>

        <div class="form-row">
            <label class="form-label" for="target_audience">Target audience definition</label>
            <textarea class="form-input form-input--textarea" id="target_audience" name="target_audience" rows="4"><?= $get('target_audience') ?></textarea>
            <span class="form-hint">Documented interpretation of “tourists visiting Kazan”; it guides the design decisions.</span>
        </div>

        <div class="form-row">
            <label class="form-label" for="copyright_owner">Copyright owner</label>
            <input class="form-input" type="text" id="copyright_owner" name="copyright_owner" value="<?= $get('copyright_owner') ?>">
            <span class="form-hint">The year in the footer is always generated from the server time.</span>
        </div>
    </section>

    <section class="panel">
        <h2 class="panel__title">Social links (Footer Social Links plugin)</h2>

        <div class="form-row">
            <label class="form-label" for="social_twitter">Twitter</label>
            <input class="form-input" type="url" id="social_twitter" name="social_twitter" value="<?= $get('social_twitter') ?>">
        </div>
        <div class="form-row">
            <label class="form-label" for="social_facebook">Facebook</label>
            <input class="form-input" type="url" id="social_facebook" name="social_facebook" value="<?= $get('social_facebook') ?>">
        </div>
        <div class="form-row">
            <label class="form-label" for="social_instagram">Instagram</label>
            <input class="form-input" type="url" id="social_instagram" name="social_instagram" value="<?= $get('social_instagram') ?>">
        </div>
    </section>

    <section class="panel">
        <h2 class="panel__title">Contact form (Static Contact Form plugin)</h2>

        <div class="form-row">
            <label class="form-label" for="contact_form_action">Form action URL</label>
            <input class="form-input" type="url" id="contact_form_action" name="contact_form_action" value="<?= $get('contact_form_action') ?>">
            <span class="form-hint">The static form-to-email service the form posts to.</span>
        </div>

        <div class="form-row">
            <label class="form-label" for="contact_email">Recipient email</label>
            <input class="form-input" type="email" id="contact_email" name="contact_email" value="<?= $get('contact_email') ?>">
        </div>

        <div class="form-row">
            <label class="form-label" for="contact_intro_text">Intro text</label>
            <input class="form-input" type="text" id="contact_intro_text" name="contact_intro_text" value="<?= $get('contact_intro_text') ?>">
        </div>

        <div class="form-row">
            <label class="form-label" for="contact_success_text">Success message</label>
            <textarea class="form-input form-input--textarea" id="contact_success_text" name="contact_success_text" rows="2"><?= $get('contact_success_text') ?></textarea>
        </div>

        <div class="form-row">
            <label class="form-label" for="contact_error_text">Error message</label>
            <textarea class="form-input form-input--textarea" id="contact_error_text" name="contact_error_text" rows="2"><?= $get('contact_error_text') ?></textarea>
            <span class="form-hint">Shown to the visitor when the message cannot be delivered.</span>
        </div>
    </section>

    <section class="panel">
        <h2 class="panel__title">Images</h2>

        <div class="form-row">
            <label class="form-label" for="login_background">Sign-in page background</label>
            <select class="form-input" id="login_background" name="login_background" data-image-picker data-preview="login-preview">
                <?php foreach ($images as $image): ?>
                    <option value="<?= Html::e($image) ?>" <?= ($values['login_background'] ?? '') === $image ? 'selected' : '' ?>>
                        <?= Html::e($image) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <img class="image-preview" id="login-preview" alt=""
                 src="<?= Html::e(Url::asset((string) ($values['login_background'] ?? ''))) ?>">
        </div>

        <div class="form-row">
            <label class="form-label" for="home_cover_image">Home page cover image</label>
            <select class="form-input" id="home_cover_image" name="home_cover_image" data-image-picker data-preview="cover-preview">
                <?php foreach ($images as $image): ?>
                    <option value="<?= Html::e($image) ?>" <?= ($values['home_cover_image'] ?? '') === $image ? 'selected' : '' ?>>
                        <?= Html::e($image) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <img class="image-preview" id="cover-preview" alt=""
                 src="<?= Html::e(Url::asset((string) ($values['home_cover_image'] ?? ''))) ?>">
        </div>

        <div class="form-row">
            <label class="form-label" for="home_gallery">Home page gallery</label>
            <textarea class="form-input form-input--textarea" id="home_gallery" name="home_gallery" rows="5"><?= $get('home_gallery') ?></textarea>
            <span class="form-hint">One image path per line.</span>
        </div>
    </section>

    <section class="panel">
        <h2 class="panel__title">Reading &amp; security</h2>

        <div class="form-row">
            <label class="form-label" for="posts_per_page">Posts per page</label>
            <input class="form-input" type="number" min="1" max="24" id="posts_per_page" name="posts_per_page" value="<?= $get('posts_per_page') ?>">
        </div>

        <div class="form-row">
            <label class="form-label" for="security_max_attempts">Failed logins before blocking</label>
            <input class="form-input" type="number" min="1" max="20" id="security_max_attempts" name="security_max_attempts" value="<?= $get('security_max_attempts') ?>">
        </div>

        <div class="form-row">
            <label class="form-label" for="security_lockout_min">Block duration (minutes)</label>
            <input class="form-input" type="number" min="1" max="1440" id="security_lockout_min" name="security_lockout_min" value="<?= $get('security_lockout_min') ?>">
        </div>
    </section>

    <p class="settings-form__actions">
        <button class="button button--primary" type="submit">Save all settings</button>
    </p>
</form>
