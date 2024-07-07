<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
        ?>
        <div class="nbar">
            <img src="media/icon/mainicon.gif" class="logo">
            <div class="admintimer">
                <input type="text" class="nobackground" id="show" value="<?= $_SESSION["timer"]; ?>" readonly>
                <form class="inline">
                    <input type="text" name="timer" id="text" value="<?= $_SESSION["timer"]; ?>">
                    <input type="submit" name="timesubmit" value="送出">
                </form>
                <input type="button" onclick="location.reload()" value="重新計時">
            </div>
            <h1 class="title">咖啡商品展示系統</h1>
        </div>
        <div class="nbar2">
            <?php
                if($_SESSION["permission"]=="管理者"){
                    ?>
                    <div class="nbarbutton">
                        <input type="button" onclick="location.href='newuser.php'" value="新增使用者">
                        <input type="button" onclick="location.href='main.php'" value="標題">
                        <input type="button" onclick="location.href='product1.html'" value="上架商品">
                        <input type="button" onclick="location.href='admin.php'" value="會員管理">
                        <input type="button" onclick="location.href='api/api.php?logout='" value="登出">
                    </div>
                    <?php
                }else{
                    ?>
                    <div class="nbarbutton">
                        <input type="button" onclick="location.href='main.php'" value="標題">
                        <input type="button" onclick="location.href='api/api.php?logout='" value="登出">
                    </div>
                    <?php
                }
            ?>
        </div><br><br><br>
        <div class="top">
            <h1>使用者管理</h1>
            <div class="center">
                查詢:
                <form class="inline">
                    <input type="text" name="search">
                    <input type="submit" name="submit" value="送出">
                </form>
            </div>
            <table class="admintable">
                <?php
                    $keyarray=["number","username","name"];

                    // 初始化排序狀態
                    if(!isset($_SESSION["sort"])){
                        $_SESSION["sort"]=["number","ASC"];
                    }

                    // 處理排序按鈕
                    if(isset($_GET["orderby"])){
                        $orderby=$_GET["orderby"];
                        $ordertype=$_GET["ordertype"];
                        // 驗證排序欄位
                        if(!in_array($orderby,$keyarray)){
                            $orderby="number";
                        }
                        // 更新session
                        $_SESSION["sort"]=[$orderby,$ordertype];
                    }

                    $orderby=$_SESSION["sort"][0];
                    $ordertype=$_SESSION["sort"][1];

                    // 排序按鈕
                    $button=[];
                    for($i=0;$i<count($keyarray);$i=$i+1){
                        $buttonordervalue="升冪";
                        $buttonordertype="ASC";
                        if($keyarray[$i]==$orderby){
                            if($ordertype=="ASC"){
                                $buttonordervalue="升冪";
                                $buttonordertype="DESC";
                            }else{
                                $buttonordervalue="降冪";
                                $buttonordertype="ASC";
                            }
                        }
                        $button[]="<input type=\"button\" onclick=\"location.href='?orderby=$keyarray[$i]&ordertype=$buttonordertype'\" value=\"$buttonordervalue\">";
                    }

                ?>
                <tr>
                    <td class="admintd">使用者編號 <?= $button[0] ?></td>
                    <td class="admintd">帳號 <?= $button[1] ?></td>
                    <td class="admintd">密碼 </td>
                    <td class="admintd">姓名 <?= $button[2] ?></td>
                    <td class="admintd">權限</td>
                    <td class="admintd">function</td>
                </tr>
                <?php
                    if(!isset($_SESSION["search"])){
                        $_SESSION["search"]="";
                    }

                    if(isset($_GET["search"])){
                        $_SESSION["search"]=$_GET["search"];
                        ?><script>location.href="admin.php"</script><?php
                    }

                    $row=query($db,"SELECT*FROM `user` WHERE `number`LIKE?OR`username`LIKE?OR`password`LIKE?OR`name`LIKE? ORDER BY `$orderby` $ordertype",["%".$_SESSION["search"]."%","%".$_SESSION["search"]."%","%".$_SESSION["search"]."%","%".$_SESSION["search"]."%"]);
                    for($i=0;$i<count($row);$i=$i+1){
                        ?>
                        <tr>
                            <td class="admintd"><?= $row[$i][5] ?></td>
                            <td class="admintd"><?= $row[$i][1] ?></td>
                            <td class="admintd"><?= $row[$i][2] ?></td>
                            <td class="admintd"><?= $row[$i][3] ?></td>
                            <td class="admintd"><?= $row[$i][4] ?></td>
                            <td class="admintd">
                                <?php
                                    if($row[$i][0]!=1){
                                        ?>
                                        <input type="button" onclick="location.href='edituser.php?edituser=&id=<?= $row[$i][0] ?>'" value="修改">
                                        <input type="button" onclick="location.href='api/api.php?deluser=&id=<?= $row[$i][0] ?>'" value="刪除">
                                        <?php
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
        </div><br><br>
        <div class="bottom">
            <h1>登入登出紀錄</h1>
            <table class="admintable">
                <tr>
                    <td class="admintd">使用者編號</td>
                    <td class="admintd">帳號</td>
                    <td class="admintd">姓名</td>
                    <td class="admintd">時間</td>
                    <td class="admintd">動作(登入/登出)</td>
                    <td class="admintd">成功/失敗</td>
                </tr>
                <?php
                    $logrow=query($db,"SELECT*FROM `log` ORDER BY `id` DESC");
                    for($i=0;$i<count($logrow);$i=$i+1){
                        ?>
                        <tr>
                            <td class="admintd"><?= $logrow[$i][1] ?></td>
                            <td class="admintd"><?= $logrow[$i][2] ?></td>
                            <td class="admintd"><?= $logrow[$i][3] ?></td>
                            <td class="admintd"><?= $logrow[$i][4] ?></td>
                            <td class="admintd"><?= $logrow[$i][5] ?></td>
                            <td class="admintd"><?= $logrow[$i][6] ?></td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
        </div>
        <div class="lightbox" id="lightbox">
            <div class="lightboxmask"></div>
            <div class="main z999">
                是否繼續操作? <br><br>
                <input type="button" onclick="location.reload()" value="Yes">
                <input type="button" onclick="location.href='api/api.php?logout='" value="否">
            </div>
        </div>
        <?php
            if(isset($_GET["submit"])){
                $_SESSION["search"]=$_GET["search"];
            }
            if(isset($_GET["timesubmit"])){
                $_SESSION["timer"]=$_GET["timer"];
                ?><script>location.href="admin.php"</script><?php
            }
        ?>
        <script src="admin.js"></script>
    </body>
</html>