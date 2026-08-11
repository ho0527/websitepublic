<?php
/**
 * 分類管理
 *
 * @var array      $categories
 * @var array|null $editing
 */

use App\Core\Csrf;
use App\Core\Html;
use App\Core\Url;

$editing = $editing ?? null;
?>
<div class="split-layout">
    <section class="panel">
        <h2 class="panel__title"><?= $editing ? 'Edit category' : 'Add new category' ?></h2>

        <form method="post" action="<?= Html::e(Url::to('admin/categories')) ?>">
            <?= Csrf::field() ?>
            <?php if ($editing): ?>
                <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
            <?php endif; ?>

            <div class="form-row">
                <label class="form-label" for="name">Name</label>
                <input class="form-input" type="text" id="name" name="name"
                       value="<?= Html::e($editing['name'] ?? '') ?>" required>
            </div>

            <div class="form-row">
                <label class="form-label" for="slug">URL slug</label>
                <input class="form-input" type="text" id="slug" name="slug" value="<?= Html::e($editing['slug'] ?? '') ?>">
                <span class="form-hint">Category URL: <code><?= Html::e(Url::to('news')) ?>&lt;slug&gt;/</code></span>
            </div>

            <div class="form-row">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-input form-input--textarea" id="description" name="description" rows="3"><?= Html::e($editing['description'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <label class="form-label" for="sort_order">Order</label>
                <input class="form-input" type="number" id="sort_order" name="sort_order"
                       value="<?= (int) ($editing['sort_order'] ?? 0) ?>">
            </div>

            <button class="button button--primary" type="submit"><?= $editing ? 'Update category' : 'Add category' ?></button>
            <?php if ($editing): ?>
                <a class="button button--ghost button--small" href="<?= Html::e(Url::to('admin/categories')) ?>">Cancel</a>
            <?php endif; ?>
        </form>
    </section>

    <section class="table-wrap">
        <table class="table">
            <caption class="screen-reader-text">All categories</caption>
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">URL</th>
                    <th scope="col">Posts</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <th scope="row">
                            <span class="table__title"><?= Html::e($category['name']) ?></span>
                            <span class="table__sub"><?= Html::e(Html::excerpt($category['description'], 70)) ?></span>
                        </th>
                        <td><code>/news/<?= Html::e($category['slug']) ?>/</code></td>
                        <td><?= (int) $category['post_count'] ?></td>
                        <td class="table__actions">
                            <a href="<?= Html::e(Url::to('news/' . $category['slug'])) ?>" target="_blank" rel="noopener">View</a>
                            <a href="<?= Html::e(Url::to('admin/categories/edit/' . $category['id'])) ?>">Edit</a>
                            <?php if ((int) $category['post_count'] === 0): ?>
                                <form method="post" action="<?= Html::e(Url::to('admin/categories/delete/' . $category['id'])) ?>"
                                      onsubmit="return confirm('Delete this category?');">
                                    <?= Csrf::field() ?>
                                    <button class="link-button link-button--danger" type="submit">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
