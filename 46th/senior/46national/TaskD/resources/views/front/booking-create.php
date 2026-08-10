<?php
/**
 * 訂票頁面。
 *
 * @var \App\Core\View                       $view
 * @var array<int, \App\Models\Station>      $stations
 * @var array<int, \App\Models\Train>        $trains
 * @var array<int, string>                   $errors
 * @var array<string, mixed>                 $old
 * @var array<string, string>                $prefill
 * @var \App\Models\CaptchaQuestion|null     $captchaQuestion
 * @var string                               $today
 */

/**
 * 取值優先序：驗證失敗時保留的輸入 > 由查詢結果帶入的值 > 預設值。
 */
$valueOf = static function (string $key, string $fallback = '') use ($old, $prefill): string {
    foreach ([$old[$key] ?? '', $prefill[$key] ?? ''] as $candidate) {
        if ((string) $candidate !== '') {
            return (string) $candidate;
        }
    }

    return $fallback;
};
?>
<h1 class="page-title">預訂車票</h1>
<p class="page-subtitle">請填寫訂票資料並完成問答驗證，送出後將以簡訊寄送訂票資訊。</p>

<?= $view->partial('partials/alerts', ['errors' => $errors]) ?>

<div class="card">
    <h2 class="card-title">訂票資料</h2>

    <form method="post" action="<?= $view->e($view->url('booking')) ?>" id="booking-form">
        <div class="field-grid">
            <div class="field">
                <label for="phone">手機號碼</label>
                <input type="tel" id="phone" name="phone" placeholder="09xxxxxxxx"
                       value="<?= $view->e($valueOf('phone')) ?>" required>
            </div>

            <div class="field">
                <label for="train_code">車次代碼</label>
                <select id="train_code" name="train_code" required>
                    <option value="">請選擇車次</option>
                    <?php foreach ($trains as $train): ?>
                        <option value="<?= $view->e($train->code) ?>"
                            <?= $valueOf('train_code') === (string) $train->code ? 'selected' : '' ?>>
                            <?= $view->e($train->code) ?>（<?= $view->e($train->type()?->name ?? '') ?>）
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="travel_date">乘車日期</label>
                <input type="date" id="travel_date" name="travel_date"
                       value="<?= $view->e($valueOf('travel_date', $today)) ?>" required>
            </div>

            <div class="field">
                <label for="from_station">起程站</label>
                <select id="from_station" name="from_station" required
                        data-selected="<?= $view->e($valueOf('from_station')) ?>">
                    <option value="">請選擇起程站</option>
                    <?php foreach ($stations as $station): ?>
                        <option value="<?= $view->e($station->code) ?>"
                            <?= $valueOf('from_station') === (string) $station->code ? 'selected' : '' ?>>
                            <?= $view->e($station->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="to_station">到達站</label>
                <select id="to_station" name="to_station" required
                        data-selected="<?= $view->e($valueOf('to_station')) ?>">
                    <option value="">請選擇到達站</option>
                    <?php foreach ($stations as $station): ?>
                        <option value="<?= $view->e($station->code) ?>"
                            <?= $valueOf('to_station') === (string) $station->code ? 'selected' : '' ?>>
                            <?= $view->e($station->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="ticket_count">車票張數 <span class="field-note">（1 ~ 1,000）</span></label>
                <input type="number" id="ticket_count" name="ticket_count" min="1" max="1000"
                       value="<?= $view->e($valueOf('ticket_count', '1')) ?>" required>
            </div>
        </div>

        <div class="field" style="margin-top: 20px;">
            <label>問答驗證碼</label>
            <div class="captcha-status">
                <button type="button" class="button button-secondary" id="captcha-open">作答</button>
                <span class="captcha-state <?= $captchaPassed ? 'is-passed' : 'is-pending' ?>" id="captcha-state">
                    <?= $captchaPassed ? '已通過驗證' : '尚未通過驗證' ?>
                </span>
            </div>
        </div>

        <div class="button-row">
            <button type="submit" class="button">送出訂票</button>
            <a class="button button-secondary" href="<?= $view->e($view->url('')) ?>">回首頁查詢車次</a>
        </div>
    </form>
</div>

<?= $view->partial('partials/captcha-dialog', ['captchaQuestion' => $captchaQuestion]) ?>

<script>
    window.railBookingConfig = {
        captchaShowUrl:    <?= json_encode($view->url('captcha'), JSON_UNESCAPED_SLASHES) ?>,
        captchaRefreshUrl: <?= json_encode($view->url('captcha/refresh'), JSON_UNESCAPED_SLASHES) ?>,
        captchaVerifyUrl:  <?= json_encode($view->url('captcha/verify'), JSON_UNESCAPED_SLASHES) ?>,
        trainStopsUrl:     <?= json_encode($view->url('booking/stops'), JSON_UNESCAPED_SLASHES) ?>
    };
</script>
<script src="<?= $view->e($view->asset('js/captcha.js')) ?>"></script>
<script src="<?= $view->e($view->asset('js/booking-form.js')) ?>"></script>
