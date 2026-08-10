<?php
/**
 * 首頁：車次查詢表單（以 GET 送出）。
 *
 * @var \App\Core\View                   $view
 * @var array<int, \App\Models\Station>   $stations
 * @var array<int, \App\Models\TrainType> $trainTypes
 * @var string                            $today
 */
?>
<h1 class="page-title">車次查詢</h1>
<p class="page-subtitle">選擇起訖車站、車種與搭乘日期，即可查詢當日可搭乘的班次與票價。</p>

<div class="card">
    <h2 class="card-title">查詢條件</h2>

    <form method="get" action="<?= $view->e($view->url('train-lookup')) ?>">
        <div class="field-grid">
            <div class="field">
                <label for="from">起程站</label>
                <select id="from" name="from" required>
                    <?php foreach ($stations as $station): ?>
                        <option value="<?= $view->e($station->code) ?>"><?= $view->e($station->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="to">到達站</label>
                <select id="to" name="to" required>
                    <?php foreach ($stations as $index => $station): ?>
                        <option value="<?= $view->e($station->code) ?>"
                            <?= $index === 1 ? 'selected' : '' ?>><?= $view->e($station->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="trainType">車種</label>
                <select id="trainType" name="trainType">
                    <option value="all">全部車種</option>
                    <?php foreach ($trainTypes as $trainType): ?>
                        <option value="<?= $view->e($trainType->id()) ?>"><?= $view->e($trainType->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="date">搭乘日期</label>
                <input type="date" id="date" name="date" value="<?= $view->e($today) ?>" required>
            </div>

            <div class="field">
                <button type="submit" class="button">查詢車次</button>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <h2 class="card-title">其他服務</h2>
    <div class="button-row" style="margin-top: 0;">
        <a class="button button-secondary" href="<?= $view->e($view->url('booking')) ?>">直接預訂車票</a>
        <a class="button button-secondary" href="<?= $view->e($view->url('bookings')) ?>">查詢我的訂票</a>
        <a class="button button-secondary" href="<?= $view->e($view->url('train-info')) ?>">查詢列車時刻</a>
        <a class="button button-secondary" href="<?= $view->e($view->url('statistics')) ?>">搭乘人數統計與開放資料</a>
    </div>
</div>
