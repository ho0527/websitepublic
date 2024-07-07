<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
        ?>
        <div class="nav" id="nav">
            <div class="title">訂房完成</div>
        </div>

        <div class="maindiv">
            <form method="POST">
                <div class="div">
                    編號: <?= $_GET["no"] ?>
                </div>
            </form>
        </div>

        <script src="init.js"></script>
    </body>
</html>