<?php
/**
 * 列車資訊：行駛星期、本週日期與各站時刻。
 *
 * @var \App\Core\View                     $view
 * @var \App\Models\Train                  $train
 * @var string                             $typeName
 * @var array<int, int>                    $serviceWeekdays
 * @var array<int, string>                 $weekdayNames
 * @var array<int, \DateTimeImmutable>     $weekDates
 * @var array<int, array<string, mixed>>   $rows
 */

$originName   = $rows[0]['station_name'] ?? '';
$terminusName = $rows === [] ? '' : $rows[count($rows) - 1]['station_name'];
?>
<h1 class="page-title"><?= $view->e($train->code) ?> 車次資訊</h1>
<p class="page-subtitle">
    <?= $view->e($typeName) ?> ·
    <?= $view->e($originName) ?> → <?= $view->e($terminusName) ?>
</p>

<div class="card">
    <h2 class="card-title">行駛星期（本週日期）</h2>

    <div class="table-scroll">
        <table class="data">
            <thead>
                <tr>
                    <?php foreach ($weekdayNames as $weekday => $name): ?>
                        <th>星期<?= $view->e($name) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php foreach ($weekdayNames as $weekday => $name): ?>
                        <?php $runs = in_array($weekday, $serviceWeekdays, true); ?>
                        <td>
                            <div><?= $view->e($weekDates[$weekday]->format('m/d')) ?></div>
                            <?php if ($runs): ?>
                                <span class="tag tag-booked">行駛</span>
                            <?php else: ?>
                                <span class="tag tag-departed">停駛</span>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h2 class="card-title">各站時刻</h2>

    <div class="table-scroll">
        <table class="data">
            <thead>
                <tr>
                    <th>站序</th>
                    <th>車站</th>
                    <th>抵達時間</th>
                    <th>發車時間</th>
                    <th>訂票</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $index => $row): ?>
                    <?php
                    // 訂票按鈕自動帶入車次；起程站預設為本站，到達站預設為終點站
                    $bookingUrl = $view->url('booking') . '?' . http_build_query([
                        'train_code'   => $train->code,
                        'from_station' => $row['station_code'],
                        'to_station'   => $rows[count($rows) - 1]['station_code'],
                    ]);
                    $isTerminus = $index === count($rows) - 1;
                    ?>
                    <tr>
                        <td><?= $view->e($row['stop_sequence']) ?></td>
                        <td><?= $view->e($row['station_name']) ?></td>
                        <td><?= $view->e($row['arrive_text']) ?></td>
                        <td><?= $view->e($row['depart_text']) ?></td>
                        <td>
                            <?php if (!$isTerminus): ?>
                                <a class="button button-small" href="<?= $view->e($bookingUrl) ?>">訂票</a>
                            <?php else: ?>
                                －
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="button-row">
        <a class="button" href="<?= $view->e($view->url('booking') . '?' . http_build_query(['train_code' => $train->code])) ?>">預訂本車次</a>
        <a class="button button-secondary" href="<?= $view->e($view->url('train-info')) ?>">查詢其他車次</a>
    </div>
</div>
