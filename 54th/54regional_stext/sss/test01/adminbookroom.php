<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>快樂旅遊網</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["signin"])){ header("location:signin.php"); }
        ?>
        <div class="nav" id="adminnav">
            <div class="title">網站管理 - 訂房管理</div>
        </div>
        <div class="nav2 center">
            <div class="btn-group">
                <input type="button" class="btn btn-outline-light" onclick="location.href='admincomment.php'" value="留言管理">
                <input type="button" class="btn btn-outline-light active" onclick="location.href='adminbookroom.php'" value="訂房管理">
                <input type="button" class="btn btn-outline-light" onclick="location.href='adminorderfood.php'" value="訂餐管理">
            </div>
        </div>

        <div class="maincard">
            <table class="table text-center">
                <tr>
                    <th>#</th>
                    <th>no</th>
                    <th>入住日期</th>
                    <th>房間編號</th>
                    <th>總金額</th>
                    <th>功能區</th>
                </tr>
                <?php
                    $row=query("SELECT*FROM `bookroom` WHERE `delete`=''");
                    for($i=0;$i<count($row);$i=$i+1){
                        ?>
                        <tr>
                            <td><?= $row[$i]["id"] ?></td>
                            <td><?= $row[$i]["no"] ?></td>
                            <td><?= $row[$i]["startday"] ?>~<?= $row[$i]["endday"] ?></td>
                            <td><?= $row[$i]["room"] ?></td>
                            <td><?= $row[$i]["price"] ?></td>
                            <td>
                                <input type="button" class="btn btn-light" onclick="location.href='editbookroom.php?id=<?= $row[$i]['id'] ?>'" value="修改">
                                <input type="button" class="btn btn-danger" onclick="if(confirm('confirm?')){ location.href='api.php?deleteroom=<?= $row[$i]['id'] ?>' }" value="刪除">
                            </td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
        </div>

        <?php
            if(isset($_POST["submit"])){
                if($_POST["username"]=="admin"&&$_POST["password"]=="1234"){
                    if($_POST["ver"]==$_POST["ans"]){
                        $_SESSION["signin"]=true;
                        ?><script>alert("登入成功");location.href="admincomment.php"</script><?php
                    }else{
                        ?><script>alert("驗證碼有誤");location.href="signin.php"</script><?php
                    }
                }else{
                    ?><script>alert("帳號或密碼有誤");location.href="signin.php"</script><?php
                }
            }
        ?>

        <script src="init.js"></script>
    </body>
</html>