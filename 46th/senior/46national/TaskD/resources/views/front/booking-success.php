<?php
/**
 * 訂票成功頁。
 *
 * @var \App\Core\View     $view
 * @var \App\Models\Booking $booking
 */

$departAt = new DateTimeImmutable((string) $booking->depart_at);
$arriveAt = new DateTimeImmutable((string) $booking->arrive_at);
?>
<h1 class="page-title">訂票成功</h1>
<p class="page-subtitle">訂票資訊已以簡訊寄送至您的手機號碼，請於乘車前妥善保存訂票編號。</p>

<div class="alert alert-success">
    訂票完成！您的訂票編號為
    <strong class="code-text"><?= $view->e($booking->booking_code) ?></strong>
</div>

<div class="card">
    <h2 class="card-title">訂票明細</h2>

    <div class="summary">
        <div>
            <dt>訂票編號</dt>
            <dd class="code-text"><?= $view->e($booking->booking_code) ?></dd>
        </div>
        <div>
            <dt>手機號碼</dt>
            <dd><?= $view->e($booking->phone) ?></dd>
        </div>
        <div>
            <dt>發車時間</dt>
            <dd><?= $view->e($departAt->format('Y/m/d H:i')) ?></dd>
        </div>
        <div>
            <dt>抵達時間</dt>
            <dd><?= $view->e($arriveAt->format('Y/m/d H:i')) ?></dd>
        </div>
        <div>
            <dt>車次</dt>
            <dd class="code-text"><?= $view->e($booking->train()?->code ?? '') ?></dd>
        </div>
        <div>
            <dt>起訖站</dt>
            <dd>
                <?= $view->e($booking->fromStation()?->name ?? '') ?>
                →
                <?= $view->e($booking->toStation()?->name ?? '') ?>
            </dd>
        </div>
        <div>
            <dt>張數</dt>
            <dd><?= $view->e($booking->ticket_count) ?> 張</dd>
        </div>
        <div>
            <dt>車票單價</dt>
            <dd><?= $view->e($booking->unit_price) ?> 元</dd>
        </div>
        <div>
            <dt>總票價</dt>
            <dd class="is-large"><?= $view->e($booking->total_price) ?> 元</dd>
        </div>
    </div>

    <div class="button-row">
        <a class="button" href="<?= $view->e($view->url('bookings') . '?' . http_build_query(['keyword' => $booking->booking_code])) ?>">查詢此筆訂票</a>
        <a class="button button-secondary" href="<?= $view->e($view->url('')) ?>">回首頁</a>
    </div>
</div>
