<?php
/**
 * 分頁列。
 *
 * @var \App\Core\View      $view
 * @var \App\Core\Paginator $paginator
 */
?>
<?php if ($paginator->totalItems() > 0): ?>
    <div class="pagination">
        <?php if ($paginator->currentPage() > 1): ?>
            <a href="<?= $view->e($paginator->urlForPage($paginator->currentPage() - 1)) ?>">上一頁</a>
        <?php endif; ?>

        <?php foreach ($paginator->pageNumbers() as $pageNumber): ?>
            <?php if ($pageNumber === $paginator->currentPage()): ?>
                <span class="is-current"><?= $view->e($pageNumber) ?></span>
            <?php else: ?>
                <a href="<?= $view->e($paginator->urlForPage($pageNumber)) ?>"><?= $view->e($pageNumber) ?></a>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($paginator->currentPage() < $paginator->totalPages()): ?>
            <a href="<?= $view->e($paginator->urlForPage($paginator->currentPage() + 1)) ?>">下一頁</a>
        <?php endif; ?>

        <span class="pagination-summary">
            第 <?= $view->e($paginator->currentPage()) ?> / <?= $view->e($paginator->totalPages()) ?> 頁，
            共 <?= $view->e($paginator->totalItems()) ?> 筆
        </span>
    </div>
<?php endif; ?>
