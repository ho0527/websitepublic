<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <input type="button" onclick="location.href='newedituser.php'" value="新增使用者">
        <input type="button" onclick="location.href='main.php'" value="首頁">
        <input type="button" onclick="location.href='productindex.php'" value="上架商品">
        <input type="button" onclick="location.href='admin.php'" value="會員管理">
        <input type="button" onclick="location.href='api.php?logout='" value="登出">
        <br><br>
        <form>
            <input type="text" name="keyword">
            <input type="submit" name="search" value="查詢">
        </form>
        <br><br>
        <form>
            <table class="table admintable">
                <tr>
                    <?php
                        if(isset($_GET["orderby"])){
                            $ordertype=$_GET["ordertype"];
                            $word="降冪";
                            if($ordertype=="ASC"){
                                $word="升冪";
                            }
                            if($_GET["orderby"]=="number"){
                                ?>
                                <td class="td">編號 <input type="button" onclick="location.href='?orderby=number&ordertype=<?= $ordertype ?>'" value="<?= $word ?>"></td>
                                <td class="td">帳號 <input type="button" onclick="location.href='?orderby=username&ordertype=DESC'" value="升冪"></td>
                                <td class="td">密碼</td>
                                <td class="td">姓名 <input type="button" onclick="location.href='?orderby=name&ordertype=DESC'" value="升冪"></td>
                                <td class="td">權限</td>
                                <td class="td">功能</td>
                                <?php
                            }elseif($_GET["orderby"]=="username"){
                                ?>
                                <td class="td">編號 <input type="button" onclick="location.href='?orderby=number&ordertype=DESC'" value="升冪"></td>
                                <td class="td">帳號 <input type="button" onclick="location.href='?orderby=number&ordertype=<?= $ordertype ?>'" value="<?= $word ?>"></td>
                                <td class="td">密碼</td>
                                <td class="td">姓名 <input type="button" onclick="location.href='?orderby=name&ordertype=DESC'" value="升冪"></td>
                                <td class="td">權限</td>
                                <td class="td">功能</td>
                                <?php
                            }elseif($_GET["orderby"]=="name"){
                                ?>
                                <td class="td">編號 <input type="button" onclick="location.href='?orderby=number&ordertype=DESC'" value="升冪"></td>
                                <td class="td">帳號 <input type="button" onclick="location.href='?orderby=username&ordertype=DESC'" value="升冪"></td>
                                <td class="td">密碼</td>
                                <td class="td">姓名 <input type="button" onclick="location.href='?orderby=name&ordertype=<?= $ordertype ?>'" value="<?= $word ?>"></td>
                                <td class="td">權限</td>
                                <td class="td">功能</td>
                                <?php
                            }
                        }else{
                            ?>
                            <td class="td">編號 <input type="button" onclick="location.href='?orderby=number&ordertype=DESC'" value="升冪"></td>
                            <td class="td">帳號 <input type="button" onclick="location.href='?orderby=username&ordertype=DESC'" value="升冪"></td>
                            <td class="td">密碼</td>
                            <td class="td">姓名 <input type="button" onclick="location.href='?orderby=name&ordertype=DESC'" value="升冪"></td>
                            <td class="td">權限</td>
                            <td class="td">功能</td>
                            <?php
                        }
                    ?>
                </tr>
                <?php
                    include("link.php");
                    if(!isset($_SESSION["keyword"])){ $_SESSION["keyword"]=""; }
                    if(isset($_GET["keyword"])){
                        $_SESSION["keyword"]=$_GET["keyword"];
                        ?><script>location.href="admin.php"</script><?php
                    }
                    $keyword=$_SESSION["keyword"];
                    $orderby="number";
                    $ordertype="ASC";
                    if(isset($_GET["orderby"])){
                        $orderby=$_GET["orderby"];
                        $ordertype=$_GET["ordertype"];
                    }
                    $row=query($db,"SELECT*FROM `user` WHERE `username`LIKE?OR`password`LIKE?OR`name`LIKE?OR`number`LIKE?OR`permission`LIKE? ORDER BY `$orderby` $ordertype",["%$keyword%","%$keyword%","%$keyword%","%$keyword%","%$keyword%"]);
                    for($i=0;$i<count($row);$i=$i+1){
                        if($row[$i][0]=="1"){
                            ?>
                            <tr>
                                <td class="td"><?= $row[$i][4] ?></td>
                                <td class="td"><?= $row[$i][1] ?></td>
                                <td class="td"><?= $row[$i][2] ?></td>
                                <td class="td"><?= $row[$i][3] ?></td>
                                <td class="td"><?= $row[$i][5] ?></td>
                                <td class="td">
                                    <input type="button" value="修改" disabled>
                                    <input type="button" value="刪除" disabled>
                                </td>
                            </tr>
                            <?php
                        }else{
                            ?>
                            <tr>
                                <td class="td"><?= $row[$i][4] ?></td>
                                <td class="td"><?= $row[$i][1] ?></td>
                                <td class="td"><?= $row[$i][2] ?></td>
                                <td class="td"><?= $row[$i][3] ?></td>
                                <td class="td"><?= $row[$i][5] ?></td>
                                <td class="td">
                                    <input type="button" onclick="location.href='newedituser.php?edit=<?= $row[$i][0] ?>'" value="修改">
                                    <input type="button" onclick="location.href='newedituser.php?del=<?= $row[$i][0] ?>'" value="刪除">
                                </td>
                            </tr>
                            <?php
                        }
                    }
                ?>
            </table><br><br>
        </form>
        <table class="table admintable">
            <tr>
                <td class="td">使用者編號</td>
                <td class="td">帳號</td>
                <td class="td">姓名</td>
                <td class="td">動作(登入/登出)</td>
                <td class="td">成功/失敗</td>
                <td class="td">時間</td>
            </tr>
            <?php
                $row=query($db,"SELECT*FROM `data` ORDER BY `id` DESC");
                for($i=0;$i<count($row);$i=$i+1){
                    ?>
                    <tr>
                        <td class="td"><?= $row[$i][1] ?></td>
                        <td class="td"><?= $row[$i][2] ?></td>
                        <td class="td"><?= $row[$i][3] ?></td>
                        <td class="td"><?= $row[$i][4] ?></td>
                        <td class="td"><?= $row[$i][5] ?></td>
                        <td class="td"><?= $row[$i][6] ?></td>
                    </tr>
                    <?php
                }
            ?>
        </table>
        <div class="timerdiv">
            <div class="timershow" id="timershow"><?= $_SESSION["timer"] ?></div>
            <form>
                <input type="text" name="time" id="time" value="<?= $_SESSION["timer"] ?>">
                <input type="submit" name="timer" value="送出">
            </form>
            <input type="button" onclick="location.href='admin.php'" value="重新計時">
        </div>
        <?php
            if(isset($_GET["timer"])){
                $_SESSION["timer"]=$_GET["time"];
                ?><script>location.href="admin.php"</script><?php
            }
        ?>
        <div class="lightbox" id="lightbox">
            <div class="mask"></div>
            <div class="lightboxmain">
                <h1>是否繼續操作?</h1>
                <input type="button" onclick="location.reload()" value="Yes">
                <input type="button" onclick="location.href='api.php?logout='" value="否">
            </div>
        </div>
        <script src="admin.js"></script>
    </body>
</html>