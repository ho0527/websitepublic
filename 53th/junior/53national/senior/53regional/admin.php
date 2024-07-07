<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>管理者專區</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            include("admindef.php");
            if(!isset($_SESSION["data"])||$_SESSION["permission"]!="管理者"){ header("location:index.php"); }
            unset($_SESSION["edit"]);
            unset($_SESSION["del"]);
        ?>
        <h1>咖啡商品展示系統</h1>
        <input type="button" class="button" onclick="location.href='main.php'" value="首頁">
        <input type="button" class="button" onclick="location.href='productindex.php'" value="電競活動管理精靈">
        <input type="button" class="button selectbutton" onclick="location.href='admin.php'" value="會員管理">
        <input type="button" class="button logout" onclick="location.href='link.php?logout='" value="登出">
        <hr>
        <div class="timer">
            <form>
                <table class="timertable">
                    <tr>
                        <td class="timertd" rowspan="2"><input type="text" class="maintimer" id="timer" value="<?= @$_SESSION["timer"] ?>" readonly></td>
                        <td class="timertd">
                            <input type="number" name="timer" value="<?= @$_SESSION["timer"] ?>">
                            <input type="submit" name="ref" value="送出">
                        </td>
                    </tr>
                    <tr>
                        <td class="timertd"><input type="button" onclick="location.reload()" value="重新計時"></td>
                    </tr>
                </table>
            </form>
        </div><br>
        <div class="adminmain mag">
            <h2>會員管理</h2>
            <input type="button" onclick="location.href='signupedit.php'" value="新增使用者">
            <form>
                <input type="text" name="search">
                <input type="submit" name="searchs" value="查尋">
            </form><br><br>
            <form>
                <table class="admintable">
                    <tr>
                        <td class="admintd">編號<input type="submit" name="udnb" id="udnb" value="升冪"></td>
                        <td class="admintd">帳號<input type="submit" name="udun" id="udun" value="升冪"></td>
                        <td class="admintd">密碼</td>
                        <td class="admintd">姓名<input type="submit" name="udn" id="udn" value="升冪"></td>
                        <td class="admintd">權限</td>
                    </tr>
                    <?php
                        if(isset($_SESSION["search"])){
                            $type=$_SESSION["search"];
                            updown(fetchall(query($db,"SELECT*FROM `user` WHERE `number`LIKE'%$type%'or`username`LIKE'%$type%'or`password`LIKE'%$type%'or`name`LIKE'%$type%'or`permission`LIKE'%$type%'")));
                        }else{
                            updown(fetchall(query($db,"SELECT*FROM `user`")));
                        }
                    ?>
                </table>
            </form>
        </div><br>
        <div class="adminmain mag">
            <h2>登入登出紀錄</h2><br>
            <table class="admintable">
                <tr>
                    <td class="admintd">使用者</td>
                    <td class="admintd">動作(登出/登入)</td>
                    <td class="admintd">成功/失敗</td>
                    <td class="admintd">時間</td>
                </tr>
                <?php
                $row=fetchall(query($db,"SELECT*FROM `data`"));
                for($i=0;$i<count($row);$i++){
                    ?>
                    <tr>
                        <td class="admintd"><?= $row[$i][1] ?></td>
                        <td class="admintd"><?= $row[$i][2] ?></td>
                        <td class="admintd"><?= $row[$i][3] ?></td>
                        <td class="admintd"><?= $row[$i][4] ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        <div class="lightbox">
            <div class="lightboxbody">
                是否繼續操作?<br>
                <input type="button" onclick="location.reload()" value="Yes">
                <input type="button" onclick="location.href='link.php?logout='" value="否">
            </div>
        </div>
        <?php
            if(isset($_GET["searchs"])){
                $_SESSION["search"]=$_GET["search"];
                ?><script>location.href="admin.php"</script><?php
            }
            if(isset($_GET["ref"])){
                $timer=$_GET["timer"];
                if($timer<=0){
                    ?><script>alert("禁止輸入小於等於0的時間")</script><?php
                }else{
                    $_SESSION["timer"]=$timer;
                }
                ?><script>location.href="admin.php"</script><?php
            }
        ?>
        <script src="timer.js"></script>
    </body>
</html>