<?php
/**
 * 錯誤與通知訊息。
 *
 * @var \App\Core\View        $view
 * @var array<int, string>    $errors
 * @var string|null           $notice
 */

$errors = $errors ?? [];
$notice = $notice ?? null;
?>
<?php if ($errors !== []): ?>
    <div class="alert alert-error" role="alert">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $view->e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($notice !== null && $notice !== ''): ?>
    <div class="alert alert-success" role="status"><?= $view->e($notice) ?></div>
<?php endif; ?>
