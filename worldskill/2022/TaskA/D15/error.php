<?php
/**
 * define the number of ../ to get to the root folder
 */
define('ROOT_LEVEL', '');

/**
 * require the general functions file
 */
require(ROOT_LEVEL . 'include/functions.php');
?>

<html>
<head>
    <link rel="stylesheet" href="provided-assets/bootstrap-4.4.1-dist/css/bootstrap.min.css">
    <script src="provided-assets/jquery-3.4.1.js"></script>
    <script src="provided-assets/bootstrap-4.4.1-dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

<div class="container">
    <div class="row">
        <div class="col">
            登入失敗 連續錯誤3次
            <input type="button" onclick="location.href='index.php'" value="返回">
        </div>

    </div>
</div>

</body>


</html>