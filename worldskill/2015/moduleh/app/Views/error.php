<?php
/**
 * 錯誤頁（例如 404）
 *
 * @var string $title
 * @var string $message
 */

use App\Core\Url;
use App\Core\View;
?>
                <h1 class="page-header"><?= View::e($title) ?></h1>
                <div class="alert alert-danger"><?= View::e($message) ?></div>
                <p><a class="btn btn-default" href="<?= View::e(Url::to()) ?>">Back to the homepage</a></p>
