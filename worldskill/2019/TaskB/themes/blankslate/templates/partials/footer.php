<?php
/**
 * 父主題頁尾（無設計的最小實作）；子主題會覆寫此檔。
 *
 * @var \App\Core\App $app
 */

use App\Core\Html;
?>
<footer class="site-footer">
    <?php $app->hooks->doAction('footer_social'); ?>
    <p>Copyright &copy; <?= date('Y') ?> - All rights reserved</p>
</footer>
