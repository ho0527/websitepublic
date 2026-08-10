<?php
/**
 * 搭乘人數統計圖表與開放資料連結。
 *
 * @var \App\Core\View                    $view
 * @var array<int, \App\Models\TrainType> $trainTypes
 * @var array<string, mixed>              $filters
 * @var array<string, mixed>              $data
 * @var string                            $jsonUrl
 * @var string                            $yesterday
 */

// 依車站彙總進出站人數，做為長條圖的資料來源
$stationTitles = [];

foreach ($data['stations'] as $station) {
    $stationTitles[$station['id']] = $station['title'];
}

$totals = [];

foreach ($data['records'] as $record) {
    $stationId = $record['station_id'];

    if (!isset($totals[$stationId])) {
        $totals[$stationId] = ['entrance' => 0, 'exit' => 0];
    }

    foreach ($record['record'] as $entry) {
        $totals[$stationId]['entrance'] += $entry['entrance'];
        $totals[$stationId]['exit']     += $entry['exit'];
    }
}

$peak = 0;

foreach ($totals as $counts) {
    $peak = max($peak, $counts['entrance'], $counts['exit']);
}
?>
<h1 class="page-title">搭乘人數統計</h1>
<p class="page-subtitle">
    統計昨天（<?= $view->e($yesterday) ?>）含以前、未被取消的訂票紀錄，
    時間以 30 分鐘為一個單位分組。
</p>

<div class="card">
    <h2 class="card-title">過濾條件</h2>

    <form method="get" action="<?= $view->e($view->url('statistics')) ?>">
        <div class="field-grid">
            <div class="field">
                <label for="trainType">車種</label>
                <select id="trainType" name="trainType">
                    <option value="">全部車種</option>
                    <?php foreach ($trainTypes as $trainType): ?>
                        <option value="<?= $view->e($trainType->id()) ?>"
                            <?= $filters['train_type_id'] === $trainType->id() ? 'selected' : '' ?>>
                            <?= $view->e($trainType->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="from">起始日期</label>
                <input type="date" id="from" name="from" value="<?= $view->e($filters['from_date'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="to">結束日期 <span class="field-note">（最晚為昨天）</span></label>
                <input type="date" id="to" name="to" max="<?= $view->e($yesterday) ?>"
                       value="<?= $view->e($filters['to_date'] ?? '') ?>">
            </div>

            <div class="field">
                <button type="submit" class="button">套用</button>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <!-- 開放資料的 JSON 超連結放在圖表上方 -->
    <div class="chart-toolbar">
        <h2 class="card-title" style="margin: 0; border: none; padding: 0;">各車站進出站人數</h2>
        <a class="json-link" href="<?= $view->e($jsonUrl) ?>" target="_blank" rel="noopener">
            ⭳ 下載開放資料 JSON
        </a>
    </div>

    <?php if ($totals === []): ?>
        <div class="empty-state">目前尚無可供統計的訂票紀錄。</div>
    <?php else: ?>
        <div class="chart-legend">
            <span><i class="swatch-entrance"></i>進站人數</span>
            <span><i class="swatch-exit"></i>離站人數</span>
        </div>

        <?php
        // 以 SVG 繪製長條圖，不依賴任何外部函式庫
        $barGroupWidth = 62;
        $chartHeight   = 260;
        $plotHeight    = 200;
        $chartWidth    = max(420, count($totals) * $barGroupWidth + 60);
        ?>
        <div class="chart-scroll">
            <svg width="<?= $view->e($chartWidth) ?>" height="<?= $view->e($chartHeight) ?>"
                 viewBox="0 0 <?= $view->e($chartWidth) ?> <?= $view->e($chartHeight) ?>"
                 role="img" aria-label="各車站進出站人數長條圖">
                <!-- 基準線 -->
                <line class="chart-axis" x1="40" y1="<?= $view->e($plotHeight + 10) ?>"
                      x2="<?= $view->e($chartWidth - 10) ?>" y2="<?= $view->e($plotHeight + 10) ?>"
                      stroke-width="1"/>

                <?php $columnIndex = 0; ?>
                <?php foreach ($totals as $stationId => $counts): ?>
                    <?php
                    $groupLeft     = 50 + $columnIndex * $barGroupWidth;
                    $entranceScale = $peak === 0 ? 0 : (int) round($counts['entrance'] / $peak * $plotHeight);
                    $exitScale     = $peak === 0 ? 0 : (int) round($counts['exit'] / $peak * $plotHeight);
                    $columnIndex++;
                    ?>
                    <rect class="bar-entrance"
                          x="<?= $view->e($groupLeft) ?>" y="<?= $view->e($plotHeight + 10 - $entranceScale) ?>"
                          width="20" height="<?= $view->e($entranceScale) ?>" rx="3">
                        <title><?= $view->e($stationTitles[$stationId] ?? '') ?> 進站 <?= $view->e($counts['entrance']) ?> 人</title>
                    </rect>
                    <rect class="bar-exit"
                          x="<?= $view->e($groupLeft + 23) ?>" y="<?= $view->e($plotHeight + 10 - $exitScale) ?>"
                          width="20" height="<?= $view->e($exitScale) ?>" rx="3">
                        <title><?= $view->e($stationTitles[$stationId] ?? '') ?> 離站 <?= $view->e($counts['exit']) ?> 人</title>
                    </rect>
                    <text class="chart-label" x="<?= $view->e($groupLeft + 21) ?>" y="<?= $view->e($plotHeight + 28) ?>"
                          text-anchor="middle" font-size="12">
                        <?= $view->e($stationTitles[$stationId] ?? '') ?>
                    </text>
                <?php endforeach; ?>

                <!-- 縱軸刻度 -->
                <text class="chart-label" x="34" y="<?= $view->e($plotHeight + 14) ?>" text-anchor="end" font-size="11">0</text>
                <text class="chart-label" x="34" y="16" text-anchor="end" font-size="11"><?= $view->e($peak) ?></text>
            </svg>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h2 class="card-title">
        統計明細
        <span class="card-hint">共 <?= $view->e(count($data['records'])) ?> 筆時間區間</span>
    </h2>

    <?php if ($data['records'] === []): ?>
        <div class="empty-state">目前尚無可供統計的訂票紀錄。</div>
    <?php else: ?>
        <div class="table-scroll">
            <table class="data">
                <thead>
                    <tr>
                        <th>車站</th>
                        <th>統計時間</th>
                        <?php foreach ($trainTypes as $trainType): ?>
                            <th class="numeric"><?= $view->e($trainType->name) ?> 進站</th>
                            <th class="numeric"><?= $view->e($trainType->name) ?> 離站</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['records'] as $record): ?>
                        <?php $moment = (new DateTimeImmutable())->setTimestamp($record['time']); ?>
                        <tr>
                            <td><?= $view->e($stationTitles[$record['station_id']] ?? '') ?></td>
                            <td><?= $view->e($moment->format('Y/m/d H:i')) ?></td>
                            <?php foreach ($record['record'] as $entry): ?>
                                <td class="numeric"><?= $view->e($entry['entrance']) ?></td>
                                <td class="numeric"><?= $view->e($entry['exit']) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
