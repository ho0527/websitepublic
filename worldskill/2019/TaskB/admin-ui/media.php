<?php
/**
 * 媒體庫
 *
 * @var array $images
 */

use App\Core\Csrf;
use App\Core\Html;
use App\Core\Url;
?>
<section class="panel">
    <h2 class="panel__title">Upload an image</h2>
    <form method="post" enctype="multipart/form-data" class="upload-form">
        <?= Csrf::field() ?>
        <div class="form-row">
            <label class="form-label" for="image">Image file</label>
            <input class="form-input" type="file" id="image" name="image" accept="image/*" required>
            <span class="form-hint">JPG, PNG, GIF, WEBP or SVG.</span>
        </div>
        <div class="form-row">
            <label class="form-label" for="folder">Folder</label>
            <select class="form-input" id="folder" name="folder">
                <option value="museums">uploads/museums</option>
                <option value="others">uploads/others</option>
            </select>
        </div>
        <button class="button button--primary" type="submit">Upload</button>
    </form>
</section>

<p class="toolbar__info"><?= count($images) ?> files in the library</p>

<ul class="media-grid">
    <?php foreach ($images as $image): ?>
        <li class="media-grid__item">
            <img src="<?= Html::e(Url::asset($image)) ?>" alt="<?= Html::e(basename($image)) ?>" loading="lazy">
            <code class="media-grid__path"><?= Html::e($image) ?></code>
        </li>
    <?php endforeach; ?>
</ul>
