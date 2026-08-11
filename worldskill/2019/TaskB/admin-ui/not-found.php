<?php
/**
 * 後台 404
 */

use App\Core\Html;
use App\Core\Url;
?>
<section class="panel">
    <h2 class="panel__title">Nothing here</h2>
    <p>That control panel page does not exist.</p>
    <p><a href="<?= Html::e(Url::to('admin')) ?>">Back to the dashboard</a></p>
</section>
