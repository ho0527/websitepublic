<?php
/**
 * 新增／編輯博物館
 *
 * @var array|null $museum
 * @var array      $errors
 * @var array      $categories
 * @var array      $images
 */

use App\Core\Csrf;
use App\Core\Html;
use App\Core\Url;

$museum = $museum ?? [];
$value  = static fn (string $key, string $default = ''): string => Html::e((string) ($museum[$key] ?? $default));
?>
<form method="post" class="form-grid">
    <?= Csrf::field() ?>

    <div class="form-main">
        <div class="panel">
            <div class="form-row">
                <label class="form-label" for="title">Museum name</label>
                <input class="form-input" type="text" id="title" name="title" value="<?= $value('title') ?>" required>
                <?php if (isset($errors['title'])): ?>
                    <span class="form-error is-visible"><?= Html::e($errors['title']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <label class="form-label" for="slug">URL slug (permalink)</label>
                <input class="form-input" type="text" id="slug" name="slug" value="<?= $value('slug') ?>"
                       aria-describedby="slug-hint">
                <span class="form-hint" id="slug-hint">
                    The page will be published at <code><?= Html::e(Url::to('')) ?><span data-slug-preview><?= $value('slug', 'museum-name') ?></span>/</code>.
                    Leave empty to generate it from the name.
                </span>
                <?php if (isset($errors['slug'])): ?>
                    <span class="form-error is-visible"><?= Html::e($errors['slug']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <label class="form-label" for="excerpt">Short summary</label>
                <textarea class="form-input form-input--textarea" id="excerpt" name="excerpt" rows="3"><?= $value('excerpt') ?></textarea>
            </div>

            <div class="form-row">
                <label class="form-label" for="content">Description</label>
                <textarea class="form-input form-input--textarea form-input--tall" id="content" name="content" rows="14" required><?= $value('content') ?></textarea>
                <span class="form-hint">Separate paragraphs with an empty line.</span>
                <?php if (isset($errors['content'])): ?>
                    <span class="form-error is-visible"><?= Html::e($errors['content']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <label class="form-label" for="gallery">Gallery images</label>
                <textarea class="form-input form-input--textarea" id="gallery" name="gallery" rows="4"><?= $value('gallery') ?></textarea>
                <span class="form-hint">One image path per line, for example <code>uploads/museums/hermitage-2.jpg</code>.</span>
            </div>
        </div>
    </div>

    <aside class="form-aside">
        <div class="panel">
            <h2 class="panel__title">Publish</h2>

            <div class="form-row">
                <label class="form-label" for="status">Status</label>
                <select class="form-input" id="status" name="status">
                    <option value="published" <?= ($museum['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= ($museum['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>

            <div class="form-row">
                <label class="checkbox">
                    <input type="checkbox" name="is_selected" value="1" <?= (int) ($museum['is_selected'] ?? 0) === 1 ? 'checked' : '' ?>>
                    Selected museum
                </label>
                <span class="form-hint">
                    Selected museums use the full page background template and show their own news posts.
                </span>
            </div>

            <div class="form-row">
                <label class="form-label" for="category_id">News category</label>
                <select class="form-input" id="category_id" name="category_id">
                    <option value="">— none —</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id'] ?>"
                            <?= (int) ($museum['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>>
                            <?= Html::e($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label class="form-label" for="sort_order">Order</label>
                <input class="form-input" type="number" id="sort_order" name="sort_order" value="<?= $value('sort_order', '0') ?>">
            </div>

            <button class="button button--primary button--block" type="submit">Save museum</button>
            <p class="panel__foot"><a href="<?= Html::e(Url::to('admin/museums')) ?>">Back to the museum list</a></p>
        </div>

        <div class="panel">
            <h2 class="panel__title">Featured photo</h2>
            <p class="form-hint">
                This photo is used as the full page background (selected museums) or as the large banner (general museums).
            </p>
            <div class="form-row">
                <label class="form-label" for="featured_image">Image</label>
                <select class="form-input" id="featured_image" name="featured_image" data-image-picker data-preview="featured-preview">
                    <option value="">— none —</option>
                    <?php foreach ($images as $image): ?>
                        <option value="<?= Html::e($image) ?>" <?= ($museum['featured_image'] ?? '') === $image ? 'selected' : '' ?>>
                            <?= Html::e($image) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <img class="image-preview" id="featured-preview" alt=""
                 src="<?= Html::e(($museum['featured_image'] ?? '') !== '' ? Url::asset((string) $museum['featured_image']) : '') ?>">
        </div>

        <div class="panel">
            <h2 class="panel__title">Visitor information</h2>
            <div class="form-row">
                <label class="form-label" for="address">Address</label>
                <input class="form-input" type="text" id="address" name="address" value="<?= $value('address') ?>">
            </div>
            <div class="form-row">
                <label class="form-label" for="opening_hours">Opening hours</label>
                <input class="form-input" type="text" id="opening_hours" name="opening_hours" value="<?= $value('opening_hours') ?>">
            </div>
        </div>
    </aside>
</form>
