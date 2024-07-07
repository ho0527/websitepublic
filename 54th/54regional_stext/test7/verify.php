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
            if(!isset($_SESSION["signin"])){
                header("location: signin.php");
            }
        ?>
        <div class="nav" id="nav">
            <div class="title">網站管理 - 進階驗證</div>
        </div>

        <div class="main">
            <div class="div verify" id="main">
                <table class="textcenter">
                    <tr>
                        <td id="1" data-word=""></td>
                        <td id="2" data-word=""></td>
                        <td id="3" data-word=""></td>
                        <td id="4" data-word=""></td>
                    </tr>
                    <tr>
                        <td id="5" data-word=""></td>
                        <td id="6" data-word=""></td>
                        <td id="7" data-word=""></td>
                        <td id="8" data-word=""></td>
                    </tr>
                    <tr>
                        <td id="9" data-word=""></td>
                        <td id="10" data-word=""></td>
                        <td id="11" data-word=""></td>
                        <td id="12" data-word=""></td>
                    </tr>
                    <tr>
                        <td id="13" data-word=""></td>
                        <td id="14" data-word=""></td>
                        <td id="15" data-word=""></td>
                        <td id="16" data-word=""></td>
                    </tr>
                </table>
            </div>
            <!-- <div class="div text-center">
                <input type="submit" class="btn btn-warning" onclick="alert('驗證成功');location.href='admincomment.php'" value="送出">
            </div> -->
        </div>

        <script src="init.js"></script>
        <script src="verify.js"></script>
    </body>
</html>