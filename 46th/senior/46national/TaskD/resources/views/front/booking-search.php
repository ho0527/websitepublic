<?php
/**
 * 訂票查詢：以訂票編號或手機號碼查詢，每頁最多 3 筆。
 *
 * @var \App\Core\View           $view
 * @var string                   $keyword
 * @var bool                     $hasQueried
 * @var \App\Core\Paginator|null $paginator
 * @var array<int, string>       $errors
 * @var string|null              $notice
 * @var \DateTimeImmutable       $now
 */
?>
<h1 class="page-title">訂票查詢</h1>
<p class="page-subtitle">輸入訂票編號或手機號碼，即可查詢並管理您的訂票紀錄。</p>

<?= $view->partial('partials/alerts', ['errors' => $errors, 'notice' => $notice]) ?>

<div class="card">
    <h2 class="card-title">查詢條件</h2>

    <form method="get" action="<?= $view->e($view->url('bookings')) ?>">
        <div class="field-grid">
            <div class="field" style="grid-column: span 2;">
                <label for="keyword">訂票編號或手機號碼</label>
                <input type="text" id="keyword" name="keyword" value="<?= $view->e($keyword) ?>"
                       placeholder="例如 aB3xY9Kq2mZp 或 0912345678" required>
            </div>
            <div class="field">
                <button type="submit" class="button">查詢</button>
            </div>
        </div>
    </form>
</div>

<?php if ($hasQueried && $paginator !== null): ?>
    <div class="card">
        <h2 class="card-title">
            訂票紀錄
            <span class="card-hint">共 <?= $view->e($paginator->totalItems()) ?> 筆</span>
        </h2>

        <?php if ($paginator->isEmpty()): ?>
            <div class="empty-state">查無符合的訂票紀錄，請確認訂票編號或手機號碼是否正確。</div>
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
                                    <?php else: ?>
                                        <form method="post"
                                              action="<?= $view->e($view->url('bookings/' . rawurlencode((string) $booking->booking_code) . '/cancel')) ?>"
                                              onsubmit="return confirm('確定要取消這筆訂票嗎？');">
                                            <input type="hidden" name="keyword" value="<?= $view->e($keyword) ?>">
                                            <button type="submit" class="button button-danger button-small">取消訂票</button>
                                        </form>
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
<?php endif; ?>
