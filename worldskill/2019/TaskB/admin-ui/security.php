<?php
/**
 * 安全性：登入嘗試紀錄
 *
 * @var \App\Core\App $app
 * @var array         $attempts
 * @var int           $maxAttempts
 * @var int           $lockout
 */

use App\Core\Csrf;
use App\Core\Html;
use App\Core\Url;
?>
<?php // 由 Site Guardian 外掛輸出的說明 ?>
<?php $app->hooks->doAction('admin_security_notice'); ?>

<div class="toolbar">
    <p class="toolbar__info">
        Showing the last <?= count($attempts) ?> login attempts. Blocked after <?= (int) $maxAttempts ?>
        failures within <?= (int) $lockout ?> minutes.
    </p>
    <form method="post" action="<?= Html::e(Url::to('admin/security')) ?>"
          onsubmit="return confirm('Clear the whole login log?');">
        <?= Csrf::field() ?>
        <button class="button button--ghost button--small" type="submit">Clear log</button>
    </form>
</div>

<div class="table-wrap">
    <table class="table">
        <caption class="screen-reader-text">Login attempts</caption>
        <thead>
            <tr>
                <th scope="col">When</th>
                <th scope="col">Username tried</th>
                <th scope="col">IP address</th>
                <th scope="col">Result</th>
                <th scope="col">User agent</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($attempts)): ?>
                <tr><td colspan="5">No login attempts recorded yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($attempts as $attempt): ?>
                <tr>
                    <td><?= Html::e(Html::date($attempt['created_at'], 'j M Y H:i:s')) ?></td>
                    <th scope="row"><?= Html::e($attempt['username']) ?></th>
                    <td><code><?= Html::e($attempt['ip_address']) ?></code></td>
                    <td>
                        <span class="tag tag--<?= (int) $attempt['is_success'] === 1 ? 'published' : 'danger' ?>">
                            <?= (int) $attempt['is_success'] === 1 ? 'success' : 'failed' ?>
                        </span>
                    </td>
                    <td class="table__ua"><?= Html::e(Html::excerpt($attempt['user_agent'], 60)) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
