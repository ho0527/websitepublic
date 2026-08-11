<?php
/**
 * 共用版面：完全沿用官方樣板（Bootstrap 3 + WorldSkills 佈景）的結構與樣式，
 * 僅把靜態內容換成動態輸出。
 *
 * @var string   $content    已算繪的頁面內容
 * @var string   $pageTitle  頁面標題
 * @var string[] $breadcrumb 麵包屑（不含 Home）
 */

use App\Core\Url;
use App\Core\View;

$pageTitle  = $pageTitle  ?? 'Restaurant Service';
$breadcrumb = $breadcrumb ?? [];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title><?= View::e($pageTitle) ?> - Guests for Restaurant Service</title>
        <meta name="description" content="WorldSkills Restaurant Service booking request system">

        <!-- Bootstrap core CSS（官方樣板提供，全部為本機檔案，不使用 CDN） -->
        <link href="<?= View::e(Url::asset('dist/css/bootstrap.min.css')) ?>" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="<?= View::e(Url::asset('restaurantapp.css')) ?>" rel="stylesheet">

        <!--[if lt IE 9]>
          <script src="<?= View::e(Url::asset('dist/js/html5shiv.3.7.0.js')) ?>"></script>
          <script src="<?= View::e(Url::asset('dist/js/respond.min.1.3.0.js')) ?>"></script>
        <![endif]-->
    </head>
    <body>
        <a class="sr-only sr-only-focusable" href="#content" tabindex="1"><div class="container"><span class="skiplink-text">Skip to main content</span></div></a>

        <div class="navbar navbar-worldskills navbar-static-top">
            <div class="cube-container">
                <div class="cube-right-bottom-blue">&nbsp;</div>
            </div>
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?= View::e(Url::to()) ?>">Reservations</a>
                </div>
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="<?= View::e(Url::to()) ?>">Information</a></li>
                        <li><a href="<?= View::e(Url::to('booking/contact')) ?>">Booking</a></li>
                        <li><a href="<?= View::e(Url::management()) ?>">Management</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="container">

            <ol class="breadcrumb">
                <li><a href="<?= View::e(Url::to()) ?>">Home</a></li>
                <?php foreach ($breadcrumb as $index => $crumb): ?>
                    <li<?= $index === count($breadcrumb) - 1 ? ' class="active"' : '' ?>><?= View::e($crumb) ?></li>
                <?php endforeach; ?>
            </ol>

            <div id="content">
<?= $content ?>
            </div>

            <footer>
                <hr class="hr-extended" />
                <p>&copy; 2015 WorldSkills</p>
            </footer>

        </div>

        <!-- Bootstrap core JS（本機檔案） -->
        <script src="<?= View::e(Url::asset('dist/js/jquery.min.1.11.1.js')) ?>"></script>
        <script src="<?= View::e(Url::asset('dist/js/bootstrap.min.js')) ?>"></script>
        <script src="<?= View::e(Url::asset('restaurantapp.js')) ?>"></script>
    </body>
</html>
