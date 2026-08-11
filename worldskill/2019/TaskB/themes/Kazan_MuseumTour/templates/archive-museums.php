<?php
/**
 * 全部博物館列表頁
 *
 * @var \App\Core\Theme $theme
 * @var array           $selectedMuseums
 * @var array           $otherMuseums
 */

use App\Core\Html;
use App\Core\Url;
?>
<section class="page-intro">
    <h1 class="page-intro__title">All museums in Kazan</h1>
    <p class="page-intro__lead">
        Eight museums, from the treasures of Volga Bulgaria to a Soviet communal apartment.
        Four of them are selected museums with their own news feed.
    </p>
</section>

<section class="selected-museums" aria-labelledby="archive-selected-heading">
    <div class="section-head">
        <h2 class="section-head__title" id="archive-selected-heading">Selected museums</h2>
    </div>
    <div class="museum-grid museum-grid--two">
        <?php foreach ($selectedMuseums as $museum): ?>
            <?php $theme->partial('partials/museum-card', ['museum' => $museum]); ?>
        <?php endforeach; ?>
    </div>
</section>

<section class="other-museums" aria-labelledby="archive-other-heading">
    <div class="section-head">
        <h2 class="section-head__title" id="archive-other-heading">Other museums</h2>
    </div>
    <div class="museum-grid museum-grid--two">
        <?php foreach ($otherMuseums as $museum): ?>
            <?php $theme->partial('partials/museum-card', ['museum' => $museum]); ?>
        <?php endforeach; ?>
    </div>
</section>
