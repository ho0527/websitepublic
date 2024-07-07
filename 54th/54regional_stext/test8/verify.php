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
            if(!isset($_SESSION["signin"])){ header("location: signin.php"); }
        ?>
        <div class="nav" id="nav">
            <div class="title">進階驗證</div>
        </div>

        <div class="main">
            <div class="verify">
                <div id="1" data-word="A"></div>
                <div id="2" data-word="B"></div>
                <div id="3" data-word="C"></div>
                <div id="4" data-word="D"></div>
                <div id="5" data-word="D"></div>
                <div id="6" data-word="C"></div>
                <div id="7" data-word="B"></div>
                <div id="8" data-word="A"></div>
            </div>
            <!-- <div class="div text-center"> -->
                <!-- <input type="button" class="btn btn-warning" onclick="alert('驗證成功');location.href='admincomment.php'" value="送出"> -->
            <!-- </div> -->
        </div>

        <script src="init.js"></script>
        <script src="verify.js"></script>
    </body>
</html>