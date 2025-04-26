<?php

/**
 * define the number of ../ to get to the root folder
 */
define('ROOT_LEVEL', '');

/**
 * require the general functions file
 */
require(ROOT_LEVEL . 'include/functions.php');

/*
 * check to ensure there is a user logged in
 */
checkLogin();
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
            <h1>Welcome <?php echo $_SESSION["id"][1]; ?></h1>

            <p>This page is protected by a login.</p>

            <p><a href="<?php echo ROOT_LEVEL; ?>scripts/logout.php" class="btn btn-info">logout</a></p>

        </div>

    </div>
</div>

</body>


</html>