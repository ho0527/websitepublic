<?php
/**
 * 博物館列表
 *
 * @var array $museums
 */

use App\Core\Csrf;
use App\Core\Html;
use App\Core\Url;
?>
<div class="toolbar">
    <p class="toolbar__info"><?= count($museums) ?> museums</p>
    <a class="button button--primary button--small" href="<?= Html::e(Url::to('admin/museums/new')) ?>">Add new museum</a>
</div>

<div class="table-wrap">
    <table class="table">
        <caption class="screen-reader-text">All museum pages</caption>
        <thead>
            <tr>
                <th scope="col">Museum</th>
                <th scope="col">URL slug</th>
                <th scope="col">Template</th>
                <th scope="col">News category</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($museums as $museum): ?>
                <tr>
                    <th scope="row">
                        <span class="table__title"><?= Html::e($museum['title']) ?></span>
                        <span class="table__sub"><?= Html::e(Html::excerpt($museum['excerpt'], 80)) ?></span>
                    </th>
                    <td><code>/<?= Html::e($museum['slug']) ?>/</code></td>
                    <td>
                        <?php if ((int) $museum['is_selected'] === 1): ?>
                            <span class="tag tag--selected">Selected · full page background</span>
                        <?php else: ?>
                            <span class="tag">General · photo banner</span>
                        <?php endif; ?>
                    </td>
                    <td><?= Html::e($museum['category_name'] ?? '—') ?></td>
                    <td><span class="tag tag--<?= Html::e($museum['status']) ?>"><?= Html::e($museum['status']) ?></span></td>
                    <td class="table__actions">
                        <a href="<?= Html::e(Url::to($museum['slug'])) ?>" target="_blank" rel="noopener">View</a>
                        <a href="<?= Html::e(Url::to('admin/museums/edit/' . $museum['id'])) ?>">Edit</a>
                        <form method="post" action="<?= Html::e(Url::to('admin/museums/delete/' . $museum['id'])) ?>"
                              onsubmit="return confirm('Delete this museum? This cannot be undone.');">
                            <?= Csrf::field() ?>
                            <button class="link-button link-button--danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
