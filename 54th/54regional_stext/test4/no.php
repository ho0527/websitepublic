<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>快樂旅遊網</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php include("link.php"); ?>
        <div class="nav" id="nav">
            <div class="title">訂房完成</div>
        </div>

        <div class="maindiv" id="main">
            訂房編號:<br>
            <?= $_GET["no"] ?>
        </div>

        <script src="init.js"></script>
    </body>
</html>