<?php
/**
 * 後台訂票紀錄查詢，每頁最多 5 筆。
 *
 * @var \App\Core\View                  $view
 * @var array<string, string>           $filters
 * @var \App\Core\Paginator             $paginator
 * @var array<int, \App\Models\Station> $stations
 * @var array<int, \App\Models\Train>   $trains
 * @var array<int, string>              $errors
 * @var string|null                     $notice
 * @var \DateTimeImmutable              $now
 */
?>
<h1 class="page-title">訂票紀錄查詢</h1>
<p class="page-subtitle">未填寫的欄位不會列入過濾條件；可取消尚未發車且尚未取消的訂票。</p>

<?= $view->partial('partials/alerts', ['errors' => $errors, 'notice' => $notice]) ?>

<div class="card">
    <h2 class="card-title">查詢條件</h2>

    <form method="get" action="<?= $view->e($view->url('admin/bookings')) ?>">
        <div class="field-grid">
            <div class="field">
                <label for="travel_date">搭乘日期</label>
                <input type="date" id="travel_date" name="travel_date"
                       value="<?= $view->e($filters['travel_date']) ?>">
            </div>

            <div class="field">
                <label for="train_code">車次</label>
                <select id="train_code" name="train_code">
                    <option value="">全部車次</option>
                    <?php foreach ($trains as $train): ?>
                        <option value="<?= $view->e($train->code) ?>"
                            <?= $filters['train_code'] === (string) $train->code ? 'selected' : '' ?>>
                            <?= $view->e($train->code) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="phone">手機號碼</label>
                <input type="tel" id="phone" name="phone" value="<?= $view->e($filters['phone']) ?>">
            </div>

            <div class="field">
                <label for="booking_code">訂票編號</label>
                <input type="text" id="booking_code" name="booking_code"
                       value="<?= $view->e($filters['booking_code']) ?>">
            </div>

            <div class="field">
                <label for="from_station">搭車站</label>
                <select id="from_station" name="from_station">
                    <option value="">全部車站</option>
                    <?php foreach ($stations as $station): ?>
                        <option value="<?= $view->e($station->code) ?>"
                            <?= $filters['from_station'] === (string) $station->code ? 'selected' : '' ?>>
                            <?= $view->e($station->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="to_station">到達站</label>
                <select id="to_station" name="to_station">
                    <option value="">全部車站</option>
                    <?php foreach ($stations as $station): ?>
                        <option value="<?= $view->e($station->code) ?>"
                            <?= $filters['to_station'] === (string) $station->code ? 'selected' : '' ?>>
                            <?= $view->e($station->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <button type="submit" class="button">查詢</button>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <h2 class="card-title">
        查詢結果
        <span class="card-hint">共 <?= $view->e($paginator->totalItems()) ?> 筆</span>
    </h2>

    <?php if ($paginator->isEmpty()): ?>
        <div class="empty-state">查無符合條件的訂票紀錄，請調整查詢條件後再試一次。</div>
    <?php else: ?>
        <div class="table-scroll">
            <table class="data">
                <thead>
                    <tr>
                        <th>訂票編號</th>
                        <th>訂票時間</th>
                        <th>發車時間</th>
                        <th>車次</th>
                        <th>起訖站</th>
                        <th class="numeric">張數</th>
                        <th>狀態</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paginator->items() as $booking): ?>
                        <?php
                        $departAt  = new DateTimeImmutable((string) $booking->depart_at);
                        $createdAt = new DateTimeImmutable((string) $booking->created_at);
                        ?>
                        <tr>
                            <td class="code-text"><?= $view->e($booking->booking_code) ?></td>
                            <td><?= $view->e($createdAt->format('Y/m/d H:i')) ?></td>
                            <td><?= $view->e($departAt->format('Y/m/d H:i')) ?></td>
                            <td class="code-text"><?= $view->e($booking->train()?->code ?? '') ?></td>
                            <td>
                                <?= $view->e($booking->fromStation()?->name ?? '') ?>
                                →
                                <?= $view->e($booking->toStation()?->name ?? '') ?>
                            </td>
                            <td class="numeric"><?= $view->e($booking->ticket_count) ?></td>
                            <td>
                                <?php if ($booking->isCancelled()): ?>
                                    <span class="tag tag-cancelled">已取消</span>
                                <?php elseif (!$booking->isBeforeDeparture($now)): ?>
                                    <span class="tag tag-departed">已發車</span>
                                <?php else: ?>
                                    <span class="tag tag-booked">已訂位</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($booking->isCancelled()): ?>
                                    <?php $cancelledAt = new DateTimeImmutable((string) $booking->cancelled_at); ?>
                                    取消於 <?= $view->e($cancelledAt->format('Y/m/d H:i')) ?>
                                <?php elseif ($booking->isCancellable($now)): ?>
                                    <form method="post"
                                          action="<?= $view->e($view->url('admin/bookings/' . rawurlencode((string) $booking->booking_code) . '/cancel') . '?' . http_build_query($filters)) ?>"
                                          onsubmit="return confirm('確定要取消這筆訂票並通知乘客嗎？');">
                                        <button type="submit" class="button button-danger button-small">取消訂票</button>
                                    </form>
                                <?php else: ?>
                                    －
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?= $view->partial('partials/pagination', ['paginator' => $paginator]) ?>
    <?php endif; ?>
</div>
