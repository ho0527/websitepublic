<?php
/**
 * 子主題 404 頁
 */

use App\Core\Html;
use App\Core\Url;
?>
<section class="page-intro page-intro--center">
    <p class="page-intro__eyebrow">Error 404</p>
    <h1 class="page-intro__title">This exhibit has moved</h1>
    <p class="page-intro__lead">
        The page you were looking for is not part of the collection.
        Try the museum list or the latest news instead.
    </p>
    <p class="hero__actions">
        <a class="button button--primary" href="<?= Html::e(Url::to('')) ?>">Back to the home page</a>
        <a class="button button--ghost" href="<?= Html::e(Url::to('museums')) ?>">All museums</a>
    </p>
</section>
