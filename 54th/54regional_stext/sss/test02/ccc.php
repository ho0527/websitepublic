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
            if(!isset($_SESSION["signin"])){ header("location:signin.php"); }
        ?>
        <div class="nav" id="adminnav">
            <div class="title">快樂旅遊網</div>
        </div>
        <div class="nav2">
            <div class="btn-group">
                <input type="button" class="btn btn-outline-light" onclick="location.href='aaa.php'" value="aaa">
                <input type="button" class="btn btn-outline-light" onclick="location.href='bbb.php'" value="bbb">
                <input type="button" class="btn btn-outline-light active" onclick="location.href='ccc.php'" value="ccc">
            </div>
        </div>

        <script src="init.js"></script>
    </body>
</html>