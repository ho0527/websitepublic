<?php
/**
 * 模組 C - 找不到頁面
 */

declare(strict_types=1);

/** @var array $viewData */
$requestedPath = (string) ($viewData['path'] ?? '');
?>
<div class="wrapper">
    <h1 class="listing-title">Page not found</h1>
    <p class="listing-intro">
        There is no heritage page or folder at
        <code><?= mc_e($requestedPath === '' ? '/' : $requestedPath) ?></code>.
    </p>
    <p><a class="button-link" href="<?= mc_e(mc_url()) ?>">Back to the index listing</a></p>
</div>
