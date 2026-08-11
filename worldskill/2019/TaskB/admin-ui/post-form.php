<?php
/**
 * 新增／編輯新聞文章
 *
 * @var array|null $post
 * @var array      $errors
 * @var array      $categories
 * @var array      $images
 */

use App\Core\Csrf;
use App\Core\Html;
use App\Core\Url;

$post  = $post ?? [];
$value = static fn (string $key, string $default = ''): string => Html::e((string) ($post[$key] ?? $default));

// datetime-local 需要 YYYY-MM-DDTHH:MM 格式
$publishedAt = ($post['published_at'] ?? '') !== ''
    ? date('Y-m-d\TH:i', (int) strtotime((string) $post['published_at']))
    : date('Y-m-d\TH:i');
?>
<form method="post" class="form-grid">
    <?= Csrf::field() ?>

    <div class="form-main">
        <div class="panel">
            <div class="form-row">
                <label class="form-label" for="title">Title</label>
                <input class="form-input" type="text" id="title" name="title" value="<?= $value('title') ?>" required>
                <?php if (isset($errors['title'])): ?>
                    <span class="form-error is-visible"><?= Html::e($errors['title']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <label class="form-label" for="slug">URL slug</label>
                <input class="form-input" type="text" id="slug" name="slug" value="<?= $value('slug') ?>">
                <span class="form-hint">Post URL: <code><?= Html::e(Url::to('news')) ?>&lt;category&gt;/&lt;slug&gt;/</code></span>
                <?php if (isset($errors['slug'])): ?>
                    <span class="form-error is-visible"><?= Html::e($errors['slug']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <label class="form-label" for="excerpt">Excerpt</label>
                <textarea class="form-input form-input--textarea" id="excerpt" name="excerpt" rows="3"><?= $value('excerpt') ?></textarea>
                <span class="form-hint">Used in listings and as the meta description. Generated automatically if left empty.</span>
            </div>

            <div class="form-row">
                <label class="form-label" for="content">Content</label>
                <textarea class="form-input form-input--textarea form-input--tall" id="content" name="content" rows="14" required><?= $value('content') ?></textarea>
                <?php if (isset($errors['content'])): ?>
                    <span class="form-error is-visible"><?= Html::e($errors['content']) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <aside class="form-aside">
        <div class="panel">
            <h2 class="panel__title">Publish</h2>

            <div class="form-row">
                <label class="form-label" for="status">Status</label>
                <select class="form-input" id="status" name="status">
                    <option value="published" <?= ($post['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= ($post['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>

            <div class="form-row">
                <label class="form-label" for="published_at">Publish date</label>
                <input class="form-input" type="datetime-local" id="published_at" name="published_at" value="<?= Html::e($publishedAt) ?>">
            </div>

            <div class="form-row">
                <label class="form-label" for="category_id">Category</label>
                <select class="form-input" id="category_id" name="category_id" required>
                    <option value="">— choose —</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id'] ?>"
                            <?= (int) ($post['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>>
                            <?= Html::e($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['category_id'])): ?>
                    <span class="form-error is-visible"><?= Html::e($errors['category_id']) ?></span>
                <?php endif; ?>
            </div>

            <button class="button button--primary button--block" type="submit">Save post</button>
            <p class="panel__foot"><a href="<?= Html::e(Url::to('admin/posts')) ?>">Back to the post list</a></p>
        </div>

        <div class="panel">
            <h2 class="panel__title">Featured photo</h2>
            <div class="form-row">
                <label class="form-label" for="featured_image">Image</label>
                <select class="form-input" id="featured_image" name="featured_image" data-image-picker data-preview="featured-preview">
                    <option value="">— none —</option>
                    <?php foreach ($images as $image): ?>
                        <option value="<?= Html::e($image) ?>" <?= ($post['featured_image'] ?? '') === $image ? 'selected' : '' ?>>
                            <?= Html::e($image) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <img class="image-preview" id="featured-preview" alt=""
                 src="<?= Html::e(($post['featured_image'] ?? '') !== '' ? Url::asset((string) $post['featured_image']) : '') ?>">
        </div>
    </aside>
</form>
