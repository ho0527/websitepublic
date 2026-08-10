<?php
/**
 * 列車新增／編輯表單。
 *
 * @var \App\Core\View                    $view
 * @var \App\Models\Train|null            $train
 * @var array<int, \App\Models\TrainType> $trainTypes
 * @var array<int, \App\Models\Station>   $stations
 * @var array<int, string>                $weekdayNames
 * @var array<int, int>                   $selectedDays
 * @var array<int, array<string, int>>    $stopRows
 * @var array<string, mixed>              $old
 * @var array<int, string>                $errors
 * @var int                               $minStops
 * @var int                               $maxStops
 * @var string                            $title
 */

$action = $train === null
    ? $view->url('admin/trains')
    : $view->url('admin/trains/' . $train->id());

$codeValue      = $old['code'] ?? ($train->code ?? '');
$typeValue      = (string) ($old['train_type_id'] ?? ($train->train_type_id ?? ''));
$departValue    = $old['depart_time'] ?? substr((string) ($train->depart_time ?? ''), 0, 5);
$selectedDays   = array_map('intval', $selectedDays);
?>
<h1 class="page-title"><?= $view->e($title) ?></h1>
<p class="page-subtitle">
    行經車站至少 <?= $view->e($minStops) ?> 站（發車站與終點站）、最多 <?= $view->e($maxStops) ?> 站，
    且不可重複選取相同車站。
</p>

<?= $view->partial('partials/alerts', ['errors' => $errors]) ?>

<form method="post" action="<?= $view->e($action) ?>">
    <div class="card">
        <h2 class="card-title">基本資料</h2>

        <div class="field-grid">
            <div class="field">
                <label for="code">列車代碼</label>
                <input type="text" id="code" name="code" value="<?= $view->e($codeValue) ?>" required>
            </div>

            <div class="field">
                <label for="train_type_id">車種</label>
                <select id="train_type_id" name="train_type_id" required>
                    <option value="">請選擇車種</option>
                    <?php foreach ($trainTypes as $trainType): ?>
                        <option value="<?= $view->e($trainType->id()) ?>"
                            <?= $typeValue === (string) $trainType->id() ? 'selected' : '' ?>>
                            <?= $view->e($trainType->name) ?>（載客 <?= $view->e($trainType->capacity) ?> 人）
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="depart_time">發車時間</label>
                <input type="time" id="depart_time" name="depart_time"
                       value="<?= $view->e($departValue) ?>" required>
            </div>
        </div>

        <div class="field" style="margin-top: 18px;">
            <label>行車星期</label>
            <div class="checkbox-row">
                <?php foreach ($weekdayNames as $weekday => $name): ?>
                    <label>
                        <input type="checkbox" name="weekdays[]" value="<?= $view->e($weekday) ?>"
                            <?= in_array($weekday, $selectedDays, true) ? 'checked' : '' ?>>
                        星期<?= $view->e($name) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card-title">
            行經車站
            <span class="card-hint">第 1 站為發車站、最後一站為終點站</span>
        </h2>

        <div class="stop-row stop-head">
            <div>站序</div>
            <div>車站</div>
            <div>行駛時間（分）</div>
            <div>停留時間（分）</div>
            <div>累計票價（元）</div>
            <div></div>
        </div>

        <div id="stop-rows">
            <?php foreach ($stopRows as $index => $stopRow): ?>
                <div class="stop-row">
                    <div class="stop-index"><?= $view->e($index + 1) ?></div>
                    <div>
                        <select name="station_id[]" required>
                            <option value="">請選擇車站</option>
                            <?php foreach ($stations as $station): ?>
                                <option value="<?= $view->e($station->id()) ?>"
                                    <?= (int) $stopRow['station_id'] === $station->id() ? 'selected' : '' ?>>
                                    <?= $view->e($station->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <input type="number" name="travel_minutes[]" min="0"
                               value="<?= $view->e($stopRow['travel_minutes']) ?>">
                    </div>
                    <div>
                        <input type="number" name="stop_minutes[]" min="0"
                               value="<?= $view->e($stopRow['stop_minutes']) ?>">
                    </div>
                    <div>
                        <input type="number" name="fare_from_origin[]" min="0"
                               value="<?= $view->e($stopRow['fare_from_origin']) ?>">
                    </div>
                    <div>
                        <button type="button" class="stop-remove" title="移除本站">×</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="button-row">
            <button type="button" class="button button-secondary" id="add-stop">＋ 新增停靠站</button>
        </div>
    </div>

    <div class="button-row">
        <button type="submit" class="button">儲存列車</button>
        <a class="button button-secondary" href="<?= $view->e($view->url('admin/trains')) ?>">取消</a>
    </div>
</form>

<!-- 新增停靠站時複製這個範本 -->
<template id="stop-row-template">
    <div class="stop-row">
        <div class="stop-index"></div>
        <div>
            <select name="station_id[]" required>
                <option value="">請選擇車站</option>
                <?php foreach ($stations as $station): ?>
                    <option value="<?= $view->e($station->id()) ?>"><?= $view->e($station->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div><input type="number" name="travel_minutes[]" min="0" value="0"></div>
        <div><input type="number" name="stop_minutes[]" min="0" value="0"></div>
        <div><input type="number" name="fare_from_origin[]" min="0" value="0"></div>
        <div><button type="button" class="stop-remove" title="移除本站">×</button></div>
    </div>
</template>

<script>
    window.trainFormConfig = {
        minStops: <?= (int) $minStops ?>,
        maxStops: <?= (int) $maxStops ?>
    };
</script>
<script src="<?= $view->e($view->asset('js/train-form.js')) ?>"></script>
