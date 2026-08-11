<?php
/**
 * 儀表板
 *
 * 依規格只保留 At a Glance、Activity、Quick Draft 三個小工具，
 * 顯示與否可在「Screen Options」調整（僅管理員可見）。
 *
 * @var \App\Core\App $app
 * @var array         $widgets
 * @var array         $counts
 * @var array         $activity
 * @var array         $categories
 * @var int           $failures
 */

use App\Core\Csrf;
use App\Core\Html;
use App\Core\Url;

$allWidgets = [
    'at_a_glance' => 'At a Glance',
    'activity'    => 'Activity',
    'quick_draft' => 'Quick Draft',
];
?>
<?php if ($app->auth->can('manage_settings')): ?>
    <details class="screen-options">
        <summary>Screen Options</summary>
        <form method="post" action="<?= Html::e(Url::to('admin')) ?>" class="screen-options__form">
            <?= Csrf::field() ?>
            <input type="hidden" name="form" value="screen_options">
            <p class="screen-options__hint">Choose which widgets appear on this dashboard.</p>
            <?php foreach ($allWidgets as $key => $label): ?>
                <label class="checkbox">
                    <input type="checkbox" name="widgets[]" value="<?= Html::e($key) ?>"
                           <?= in_array($key, $widgets, true) ? 'checked' : '' ?>>
                    <?= Html::e($label) ?>
                </label>
            <?php endforeach; ?>
            <button class="button button--primary button--small" type="submit">Apply</button>
        </form>
    </details>
<?php endif; ?>

<div class="widget-grid">
    <?php if (in_array('at_a_glance', $widgets, true)): ?>
        <section class="widget" aria-labelledby="widget-glance">
            <h2 class="widget__title" id="widget-glance">At a Glance</h2>
            <ul class="glance-list">
                <li><strong><?= (int) $counts['museums'] ?></strong> Museums</li>
                <li><strong><?= (int) $counts['published'] ?></strong> Published posts</li>
                <li><strong><?= (int) $counts['drafts'] ?></strong> Drafts</li>
                <li><strong><?= (int) $counts['categories'] ?></strong> Categories</li>
                <li><strong><?= (int) $counts['media'] ?></strong> Media files</li>
                <li><strong><?= (int) $counts['plugins'] ?></strong> Active plugins</li>
                <li><strong><?= (int) $counts['admins'] ?></strong> Admins</li>
                <li><strong><?= (int) $counts['editors'] ?></strong> Editors</li>
            </ul>
            <p class="widget__note">
                Theme in use: <strong><?= Html::e($app->theme->name()) ?></strong>
                (child of <?= Html::e($app->theme->parentName()) ?>).
                <?php if ($app->auth->can('view_security')): ?>
                    <br><?= (int) $failures ?> failed login attempts recorded.
                <?php endif; ?>
            </p>
        </section>
    <?php endif; ?>

    <?php if (in_array('activity', $widgets, true)): ?>
        <section class="widget" aria-labelledby="widget-activity">
            <h2 class="widget__title" id="widget-activity">Activity</h2>
            <?php if (empty($activity)): ?>
                <p class="widget__note">Nothing has been edited yet.</p>
            <?php else: ?>
                <ul class="activity-list">
                    <?php foreach ($activity as $item): ?>
                        <li>
                            <span class="activity-list__date"><?= Html::e(Html::date($item['updated_at'], 'j M Y H:i')) ?></span>
                            <a href="<?= Html::e(Url::to('admin/posts/edit/' . $item['id'])) ?>"><?= Html::e($item['title']) ?></a>
                            <span class="tag tag--<?= Html::e($item['status']) ?>"><?= Html::e($item['status']) ?></span>
                            <span class="activity-list__meta">in <?= Html::e($item['category_name']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?php if (in_array('quick_draft', $widgets, true)): ?>
        <section class="widget" aria-labelledby="widget-draft">
            <h2 class="widget__title" id="widget-draft">Quick Draft</h2>
            <form method="post" action="<?= Html::e(Url::to('admin')) ?>">
                <?= Csrf::field() ?>
                <input type="hidden" name="form" value="quick_draft">

                <div class="form-row">
                    <label class="form-label" for="draft-title">Title</label>
                    <input class="form-input" type="text" id="draft-title" name="title" required>
                </div>

                <div class="form-row">
                    <label class="form-label" for="draft-category">Category</label>
                    <select class="form-input" id="draft-category" name="category_id">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>"><?= Html::e($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <label class="form-label" for="draft-content">What is on your mind?</label>
                    <textarea class="form-input form-input--textarea" id="draft-content" name="content" rows="5"></textarea>
                </div>

                <button class="button button--primary" type="submit">Save draft</button>
            </form>
        </section>
    <?php endif; ?>
</div>
