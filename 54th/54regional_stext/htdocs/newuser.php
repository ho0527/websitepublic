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
            <h1 class="title">咖啡商品展示系統</h1>
        </div>
        <div class="main">
            <form method="POST">
                帳號: <input type="text" name="username" id="username"><br><br>
                密碼: <input type="text" name="password" id="password"><br><br>
                姓名: <input type="text" name="name" id="name"><br><br>
                權限: <select name="permission">
                    <option value="一般使用者">一般使用者</option>
                    <option value="管理者">管理者</option>
                </select><br><br>
                <div class="buttondiv">
                    <input type="button" onclick="location.href='admin.php'" value="返回">
                    <input type="submit" name="submit" value="送出">
                </div>
            </form>
        </div>
        <?php
            if(isset($_POST["submit"])){
                $username=$_POST["username"];
                $password=$_POST["password"];
                $name=$_POST["name"];
                $permission=$_POST["permission"];
                if(!$row=query($db,"SELECT*FROM `user` WHERE `username`=?",[$username])){
                    query($db,"INSERT INTO `user`(`username`,`password`,`name`,`permission`)VALUES(?,?,?,?)",[$username,$password,$name,$permission]);
                    $row=query($db,"SELECT*FROM `user` WHERE `username`=?",[$username])[0];
                    query($db,"UPDATE `user` SET `number`=? WHERE `username`=?",[str_pad($row[0]-1,5,"0",STR_PAD_LEFT),$username]);
                    ?><script>alert("新增成功");location.href="admin.php"</script><?php
                }else{
                    ?><script>alert("使用者已存在")</script><?php
                }
            }
        ?>
        <script src="index.js"></script>
    </body>
</html>