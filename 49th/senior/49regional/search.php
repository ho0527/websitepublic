<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>翻牌配對驗證模組</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            // if(!isset($_SESSION["data"])){ header("location:index.php"); }
        ?>
        <div class="navigationbar">
            <div class="maintitle">電子競技網站管理</div>
            <div class="navigationbarbuttondiv">
                <input type="button" class="navigationbarbutton" onclick="location.href='main.php'" value="首頁">
                <input type="button" class="navigationbarbutton" onclick="location.href='productindex.html'" value="電競活動管理精靈">
                <input type="button" class="navigationbarbutton selectbutton" onclick="location.href='search.php'" value="查尋">
                <input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="會員管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
            </div>
        </div>
        <div class="mainbody">
            <table class="producttable">
                <tr>
                    <td>
                        <table class="coffeetable">
                            <tr>
                                <td class="coffee"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <script src="logincheck.js"></script>
    </body>
</html>