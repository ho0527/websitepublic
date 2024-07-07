<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>網站前台登入介面</title>
        <link rel="stylesheet" href="/index.css">
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="plugin/css/chrisplugin.css">
        <script src="plugin/js/highchart.js"></script>
        <script src="plugin/js/chrisplugin.js"></script>
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){ header("location:index.php"); }
        ?>
        <div class="navigationbar">
            <div class="navigationbarleft">
                <div class="navigationbartitle">專案討論系統</div>
            </div>
            <div class="navigationbarright">
                <?php
                    if($_SESSION["data"]==1){ ?><input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="使用者管理"><?php }
                ?>
                <input type="button" class="navigationbarbutton" onclick="location.href='project.php'" value="專案管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='teamleader.php'" value="組長功能管理">
                <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='statistic.php'" value="統計管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
            </div>
        </div>
        <div class="main statisticmain center macossectiondivy ">
            <table class="sttable">
                <tr>
                    <td class="statistictd statisticnoneselect" id="useropinion">
                        使用者意見發表統計
                    </td>
                    <td class="statistictd statisticnoneselect" id="projectopinion">
                        各專案意見發表<br>
                        總數量統計
                    </td>
                    <td class="statistictd statisticnoneselect" id="projectfacing">
                        各專案意見發表<br>
                        之各面相統計
                    </td>
                    <td class="statistictd"></td>
                    <td class="statistictd"></td>
                </tr>
                <tr class="statistictr">
                    <td class="statistictdshow" id="show" colspan="5" rowspan="8"></td>
                </tr>
            </table>
        </div>
        <script src="statistic.js"></script>
    </body>
</html>