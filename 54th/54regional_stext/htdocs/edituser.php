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
            if(isset($_GET["edituser"])){
                $_SESSION["id"]=$_GET["id"];
                $row=query($db,"SELECT*FROM `user` WHERE `id`=?",[$_SESSION["id"]]);
            }
        ?>
        <div class="nbar">
            <img src="media/icon/mainicon.gif" class="logo">
            <h1 class="title">咖啡商品展示系統</h1>
        </div>
        <div class="main">
            <form method="POST">
                密碼: <input type="text" name="password" value="<?= $row[0][2] ?>"><br><br>
                姓名: <input type="text" name="name" value="<?= $row[0][3] ?>"><br><br>
                權限: <select name="permission">
                    <?php
                        if($row[0][4]=="管理者"){
                            ?>
                            <option value="一般使用者">一般使用者</option>
                            <option value="管理者" selected>管理者</option>
                            <?php
                        }else{
                            ?>
                            <option value="一般使用者">一般使用者</option>
                            <option value="管理者">管理者</option>
                            <?php
                        }
                    ?>
                </select><br><br>
                <div class="buttondiv">
                    <input type="button" onclick="location.href='admin.php'" value="返回">
                    <input type="submit" name="submit" value="送出">
                </div>
            </form>
        </div>
        <?php
            if(isset($_POST["submit"])){
                $password=$_POST["password"];
                $name=$_POST["name"];
                $permission=$_POST["permission"];
                query($db,"UPDATE `user` SET `password`=?,`name`=?,`permission`=? WHERE `id`=?",[$password,$name,$permission,$_SESSION["id"]]);
                ?><script>alert("修改成功");location.href="admin.php"</script><?php
            }
        ?>
        <script src="index.js"></script>
    </body>
</html>