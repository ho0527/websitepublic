<?php
/**
 * 父主題 404 樣板（最小實作）；子主題會覆寫。
 */

use App\Core\Html;
use App\Core\Url;
?>
<section class="not-found">
    <h1>Page not found</h1>
    <p>The page you were looking for is not here.</p>
    <p><a href="<?= Html::e(Url::to('')) ?>">Back to the home page</a></p>
</section>
