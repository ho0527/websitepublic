<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>南港展覽館接駁專車</title>
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="plugin/css/macossection.css">
        <script src="plugin/js/macossection.js"></script>
    </head>
    <body>
        <div class="navigationbar">
            <div class="navigationbarleft">
                <img src="logo.png" class="logo">
                <div class="maintitle">南港展覽館接駁專車系統</div>
            </div>
            <div class="navigationbarright">
                <input type="button" class="navigationbarbutton" onclick="location.href='index.php'" value="首頁">
                <input type="button" class="navigationbarbutton selectbutton" onclick="location.href='login.php'" value="系統管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
            </div>
        </div>
        <?php
            include("link.php");
            unset($_SESSION["id"]);
            if(isset($_SESSION["data"])){
                ?>
                <div class="loginhead" id="head">
                    <div class="center">
                        <input type="button" class="loginbutton button" onclick="location.href='bus.php'" value="接駁車管理">
                        <input type="button" class="loginbutton button selectbutton" onclick="location.href='site.php'" value="站點管理">
                    </div>
                </div>
                <div class="adminpost" id="main">
                    <div class="postbody macossectiondiv">
                        <div class="posttitle">
                            站點管理
                            <input type="button" class="buttonlittle" onclick="location.href='edit.php?key=newsite'" value="新增">
                        </div>
                        <table class="postmessage">
                            <tr>
                                <td class="td">站點名稱</td>
                                <td class="td">行駛時間(min)</td>
                                <td class="td">停留時間(min)</td>
                                <td class="td">操作</td>
                            </tr>
                            <?php
                            $row=query($db,"SELECT*FROM `site`");
                            for($i=0;$i<count($row);$i=$i+1){
                                ?>
                                <tr>
                                    <td class="td"><?= $row[$i][1] ?></td>
                                    <td class="td"><?= $row[$i][2] ?>min</td>
                                    <td class="td"><?= $row[$i][3] ?>min</td>
                                    <td class="td">
                                        <input type="button" class="buttonlittle" onclick="location.href='edit.php?key=editsite&id=<?= $row[$i][0] ?>'" value="編輯">
                                        <input type="button" class="buttonlittle" onclick="location.href='edit.php?key=delsite&id=<?= $row[$i][0] ?>'" value="刪除">
                                    </td>
                                </tr>
                                <?php
                                }
                            ?>
                        </table>
                    </div>
                </div>
                <?php
            }else{ header("location:login.php"); }
        ?>
        <script src="login.js"></script>
    </body>
</html>