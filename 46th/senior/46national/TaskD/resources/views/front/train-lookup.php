<?php
/**
 * 車次查詢結果。
 *
 * @var \App\Core\View                    $view
 * @var array<int, \App\Models\Station>   $stations
 * @var array<int, \App\Models\TrainType> $trainTypes
 * @var array<string, string>             $conditions
 * @var array<int, array<string, mixed>>  $results
 * @var string|null                       $emptyMessage
 * @var \App\Models\Station|null          $fromStation
 * @var \App\Models\Station|null          $toStation
 * @var \DateTimeImmutable|null           $travelDate
 */
?>
<h1 class="page-title">車次查詢結果</h1>
<p class="page-subtitle">
    <?php if ($fromStation !== null && $toStation !== null && $travelDate !== null): ?>
        <?= $view->e($travelDate->format('Y/m/d')) ?> ·
        <?= $view->e($fromStation->name) ?> → <?= $view->e($toStation->name) ?>
    <?php else: ?>
        請重新確認查詢條件。
    <?php endif; ?>
</p>

<div class="card">
    <h2 class="card-title">修改查詢條件</h2>

    <form method="get" action="<?= $view->e($view->url('train-lookup')) ?>">
        <div class="field-grid">
            <div class="field">
                <label for="from">起程站</label>
                <select id="from" name="from" required>
                    <?php foreach ($stations as $station): ?>
                        <option value="<?= $view->e($station->code) ?>"
                            <?= strcasecmp((string) $station->code, $conditions['from']) === 0 ? 'selected' : '' ?>>
                            <?= $view->e($station->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="to">到達站</label>
                <select id="to" name="to" required>
                    <?php foreach ($stations as $station): ?>
                        <option value="<?= $view->e($station->code) ?>"
                            <?= strcasecmp((string) $station->code, $conditions['to']) === 0 ? 'selected' : '' ?>>
                            <?= $view->e($station->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="trainType">車種</label>
                <select id="trainType" name="trainType">
                    <option value="all" <?= $conditions['trainType'] === 'all' ? 'selected' : '' ?>>全部車種</option>
                    <?php foreach ($trainTypes as $trainType): ?>
                        <option value="<?= $view->e($trainType->id()) ?>"
                            <?= (string) $trainType->id() === $conditions['trainType'] ? 'selected' : '' ?>>
                            <?= $view->e($trainType->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="date">搭乘日期</label>
                <input type="date" id="date" name="date" value="<?= $view->e($conditions['date']) ?>" required>
            </div>

            <div class="field">
                <button type="submit" class="button">重新查詢</button>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <h2 class="card-title">
        可搭乘班次
        <span class="card-hint">共 <?= $view->e(count($results)) ?> 個班次</span>
    </h2>

    <?php if ($emptyMessage !== null): ?>
        <div class="empty-state"><?= $view->e($emptyMessage) ?></div>
    <?php else: ?>
        <div class="table-scroll">
            <table class="data">
                <thead>
                    <tr>
                        <th>車種</th>
                        <th>列車編號</th>
                        <th>發車站</th>
                        <th>終點站</th>
                        <th>預計開車時間</th>
                        <th>預計到達時間</th>
                        <th>行駛時間</th>
                        <th class="numeric">票價</th>
                        <th class="numeric">剩餘座位</th>
                        <th>訂票</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                        <?php
                        // 點選訂票時把起訖站、日期與車次一併帶入訂票頁面
                        $bookingUrl = $view->url('booking') . '?' . http_build_query([
                            'train_code'   => $result['train']->code,
                            'from_station' => $conditions['from'],
                            'to_station'   => $conditions['to'],
                            'travel_date'  => $conditions['date'],
                        ]);
                        ?>
                        <tr>
                            <td><?= $view->e($result['type_name']) ?></td>
                            <td class="code-text"><?= $view->e($result['train']->code) ?></td>
                            <td><?= $view->e($result['origin_name']) ?></td>
                            <td><?= $view->e($result['terminus_name']) ?></td>
                            <td><?= $view->e($result['depart']->format('H:i')) ?></td>
                            <td><?= $view->e($result['arrive']->format('H:i')) ?></td>
                            <td><?= $view->e($result['duration_text']) ?></td>
                            <td class="numeric"><?= $view->e($result['fare']) ?> 元</td>
                            <td class="numeric"><?= $view->e($result['available_seats']) ?></td>
                            <td>
                                <a class="button button-small" href="<?= $view->e($bookingUrl) ?>">訂票</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
