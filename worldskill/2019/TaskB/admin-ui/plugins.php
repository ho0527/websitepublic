<?php
/**
 * 外掛管理
 *
 * @var array $plugins
 */

use App\Core\Csrf;
use App\Core\Html;
use App\Core\Url;
?>
<p class="toolbar__info">
    Plugins extend the site through hooks. Deactivating a plugin removes its output from the front end immediately.
</p>

<div class="table-wrap">
    <table class="table">
        <caption class="screen-reader-text">Installed plugins</caption>
        <thead>
            <tr>
                <th scope="col">Plugin</th>
                <th scope="col">Description</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plugins as $plugin): ?>
                <?php $isActive = (int) $plugin['is_active'] === 1; ?>
                <tr>
                    <th scope="row">
                        <span class="table__title"><?= Html::e($plugin['name']) ?></span>
                        <span class="table__sub"><code>plugins/<?= Html::e($plugin['slug']) ?>/</code></span>
                    </th>
                    <td><?= Html::e($plugin['description']) ?></td>
                    <td>
                        <span class="tag tag--<?= $isActive ? 'published' : 'draft' ?>">
                            <?= $isActive ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <form method="post" action="<?= Html::e(Url::to('admin/plugins')) ?>">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="slug" value="<?= Html::e($plugin['slug']) ?>">
                            <input type="hidden" name="state" value="<?= $isActive ? 'deactivate' : 'activate' ?>">
                            <button class="button button--small <?= $isActive ? 'button--ghost' : 'button--primary' ?>" type="submit">
                                <?= $isActive ? 'Deactivate' : 'Activate' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
