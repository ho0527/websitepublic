<?php
/**
 * 新聞列表頁（全部新聞 /news/ 與分類新聞 /news/<category>/ 共用）
 *
 * @var \App\Core\Theme $theme
 * @var array|null      $category   分類資料；null 代表「全部新聞」
 * @var array           $posts
 * @var array           $categories
 * @var int             $page
 * @var int             $totalPages
 * @var string          $baseRoute
 */

use App\Core\Html;
use App\Core\Url;
?>
<section class="page-intro">
    <h1 class="page-intro__title">
        <?= Html::e($category === null ? 'News' : $category['name']) ?>
    </h1>
    <p class="page-intro__lead">
        <?= Html::e($category === null
            ? 'Everything that is happening in the museums of Kazan: site updates, seasonal events and news from each selected museum.'
            : $category['description']) ?>
    </p>
</section>

<nav class="category-filter" aria-label="News categories">
    <ul>
        <li>
            <a href="<?= Html::e(Url::to('news')) ?>"
               class="chip<?= $category === null ? ' chip--active' : '' ?>"
               <?= $category === null ? 'aria-current="page"' : '' ?>>All news</a>
        </li>
        <?php foreach ($categories as $item): ?>
            <?php $isActive = $category !== null && (int) $item['id'] === (int) $category['id']; ?>
            <li>
                <a href="<?= Html::e(Url::to('news/' . $item['slug'])) ?>"
                   class="chip<?= $isActive ? ' chip--active' : '' ?>"
                   <?= $isActive ? 'aria-current="page"' : '' ?>><?= Html::e($item['name']) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

<?php if (empty($posts)): ?>
    <p class="empty-state">There are no posts in this category yet.</p>
<?php else: ?>
    <div class="post-list post-list--grid">
        <?php foreach ($posts as $post): ?>
            <?php $theme->partial('partials/post-card', ['post' => $post, 'variant' => 'default']); ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($totalPages > 1): ?>
    <nav class="pagination" aria-label="News pagination">
        <ul>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li>
                    <a href="<?= Html::e(Url::to($baseRoute) . ($i > 1 ? '?page=' . $i : '')) ?>"
                       class="pagination__link<?= $i === $page ? ' pagination__link--current' : '' ?>"
                       <?= $i === $page ? 'aria-current="page"' : '' ?>><?= (int) $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>
