<?php
/**
 * 刪除列車前的確認頁。
 *
 * @var \App\Core\View                  $view
 * @var \App\Models\Train               $train
 * @var array<int, \App\Models\Booking> $affected
 */
?>
<h1 class="page-title">刪除車次 <?= $view->e($train->code) ?></h1>
<p class="page-subtitle">請確認以下資訊後再決定是否刪除此車次。</p>

<?php if ($affected === []): ?>
    <div class="alert alert-info">此車次目前沒有尚未發車的訂票紀錄，可以直接刪除。</div>
<?php else: ?>
    <div class="alert alert-warning">
        此車次尚有 <strong><?= $view->e(count($affected)) ?></strong> 筆未發車的訂票紀錄。
        若仍要繼續刪除，系統會自動取消這些訂票，並以簡訊通知所有受影響的乘客。
    </div>

    <div class="card">
        <h2 class="card-title">將被取消的訂票</h2>

        <div class="table-scroll">
            <table class="data">
                <thead>
                    <tr>
                        <th>訂票編號</th>
                        <th>手機號碼</th>
                        <th>發車時間</th>
                        <th>起訖站</th>
                        <th class="numeric">張數</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($affected as $booking): ?>
                        <?php $departAt = new DateTimeImmutable((string) $booking->depart_at); ?>
                        <tr>
                            <td class="code-text"><?= $view->e($booking->booking_code) ?></td>
                            <td><?= $view->e($booking->phone) ?></td>
                            <td><?= $view->e($departAt->format('Y/m/d H:i')) ?></td>
                            <td>
                                <?= $view->e($booking->fromStation()?->name ?? '') ?>
                                →
                                <?= $view->e($booking->toStation()?->name ?? '') ?>
                            </td>
                            <td class="numeric"><?= $view->e($booking->ticket_count) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="button-row" style="margin-top: 0;">
        <form method="post" action="<?= $view->e($view->url('admin/trains/' . $train->id() . '/delete')) ?>"
              onsubmit="return confirm('確定要刪除車次 <?= $view->e($train->code) ?> 嗎？此動作無法復原。');">
            <button type="submit" class="button button-danger">
                <?= $affected === [] ? '確認刪除' : '仍要刪除並取消上述訂票' ?>
            </button>
        </form>
        <a class="button button-secondary" href="<?= $view->e($view->url('admin/trains')) ?>">取消，返回列車清單</a>
    </div>
</div>
