<?php
/**
 * 新聞文章列表
 *
 * @var array $posts
 */

use App\Core\Csrf;
use App\Core\Html;
use App\Core\Url;
?>
<div class="toolbar">
    <p class="toolbar__info"><?= count($posts) ?> posts</p>
    <a class="button button--primary button--small" href="<?= Html::e(Url::to('admin/posts/new')) ?>">Add new post</a>
</div>

<div class="table-wrap">
    <table class="table">
        <caption class="screen-reader-text">All news posts</caption>
        <thead>
            <tr>
                <th scope="col">Title</th>
                <th scope="col">Category</th>
                <th scope="col">Author</th>
                <th scope="col">Published</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <th scope="row">
                        <span class="table__title"><?= Html::e($post['title']) ?></span>
                        <span class="table__sub"><code>/news/<?= Html::e($post['category_slug']) ?>/<?= Html::e($post['slug']) ?>/</code></span>
                    </th>
                    <td><?= Html::e($post['category_name']) ?></td>
                    <td><?= Html::e($post['author_name'] ?? '—') ?></td>
                    <td><?= Html::e(Html::date($post['published_at'], 'j M Y')) ?></td>
                    <td><span class="tag tag--<?= Html::e($post['status']) ?>"><?= Html::e($post['status']) ?></span></td>
                    <td class="table__actions">
                        <?php if ($post['status'] === 'published'): ?>
                            <a href="<?= Html::e(Url::to('news/' . $post['category_slug'] . '/' . $post['slug'])) ?>"
                               target="_blank" rel="noopener">View</a>
                        <?php endif; ?>
                        <a href="<?= Html::e(Url::to('admin/posts/edit/' . $post['id'])) ?>">Edit</a>
                        <form method="post" action="<?= Html::e(Url::to('admin/posts/delete/' . $post['id'])) ?>"
                              onsubmit="return confirm('Delete this post?');">
                            <?= Csrf::field() ?>
                            <button class="link-button link-button--danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
