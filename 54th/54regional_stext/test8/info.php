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
            <div class="title">交通資訊</div>
        </div>

        <div class="maindiv" style="overflow: hidden">
            <img src="media/picture/map.png" class="image map" id="map">
        </div>
        <div class="text-center">
            <input type="button" id="m" value="-">
            <input type="button" id="p" value="+">
        </div>

        <script src="init.js"></script>
        <script src="info.js"></script>
    </body>
</html>