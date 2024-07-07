<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>重設帳密</title>
        <link href="index.css" rel="Stylesheet">
    </head>
    <body>
        <?php
            include("link.php");
            // if(!isset($_SESSION["data"])||$_SESSION["permission"]!="管理者"){ header("location:index.php"); }
        ?>
        <h1>電子競技網站管理</h1>
        <input type="button" class="button" onclick="location.href='main.php'" value="首頁">
        <input type="button" class="button" onclick="location.href='productindex.html'" value="上架商品">
        <input type="button" class="button selectbutton" onclick="location.href='admin.php'" value="會員管理">
        <input type="button" class="button logout" onclick="location.href='link.php?logout='" value="登出">
        <hr>
        <?php
            if(isset($_SESSION["edit"])){
                $number=$_SESSION["edit"];
                $row=query($db,"SELECT*FROM `user` WHERE `number`='$number'");
                if($row){
                    ?>
                    <div class="main">
                        <form>
                            <h2>編輯使用者</h2>
                            <hr>
                            編號: <input type="text" class="input" name="number" value="<?php echo($row[1]); ?>" readonly><br><br>
                            姓名: <input type="text" class="input" name="name" value="<?php echo($row[4]); ?>" maxlength="1250"><br><br>
                            帳號: <input type="text" class="input" name="username" value="<?php echo($row[2]); ?>" maxlength="1250"><br><br>
                            密碼: <input type="text" class="input" name="password" value="<?php echo($row[3]); ?>" maxlength="1250"><br><br>
                            <?php
                                if($row[5]=="管理者"){
                                    ?>管理員權限<input type="checkbox" class="checkbox" name="admin" checked><?php
                                }else{
                                    ?>管理員權限<input type="checkbox" class="checkbox" name="admin"><?php
                                }
                            ?>
                            <button type="button" class="button" onclick="location.href='admin.php'">返回主頁</button>
                            <input type="submit" class="button" name="editsubmit" value="送出">
                        </form>
                    </div>
                    <?php
                }else{
                    ?><script>alert("找不到此使用者");location.href="admin.php"</script><?php
                }
            }else{
                ?>
                <div class="main">
                    <form>
                        <h2>新增使用者</h2>
                        <hr>
                        姓名: <input type="text" class="input" name="name" maxlength="1250"><br><br>
                        帳號: <input type="text" class="input" name="username" maxlength="1250"><br><br>
                        密碼: <input type="text" class="input" name="password" maxlength="1250"><br><br>
                        管理員權限<input type="checkbox" class="checkbox" name="admin">
                        <input type="button" class="button" onclick="location.href='admin.php'" value="返回主頁">
                        <input type="submit" class="button" name="signupsubmit" value="送出">
                    </form>
                </div>
                <?php
            }
        ?>
        <?php
            if(isset($_GET["signupsubmit"])){
                $name=$_GET["name"];
                $username=$_GET["username"];
                $password=$_GET["password"];
                $row=query($db,"SELECT*FROM `user` WHERE `username`='$username'");
                if($username==""||$password==""){
                    ?><script>alert("請填寫帳密!");location.href="signupedit.php"</script><?php
                }elseif($row){
                    ?><script>alert("帳號已存在");location.href="signupedit.php"</script><?php
                }else{
                    if(isset($_GET["admin"])){
                        query($db,"INSERT INTO `user`(`username`,`password`,`name`,`permission`)VALUES('$username','$password','$name','管理者')");
                    }else{
                        query($db,"INSERT INTO `user`(`username`,`password`,`name`,`permission`)VALUES('$username','$password','$name','一般使用者')");
                    }
                    $row=query($db,"SELECT*FROM `user` WHERE `username`='$username'")[0];
                    if(isset($_GET["admin"])){
                        $number="a".str_pad($row[0]-1,4,"0",STR_PAD_LEFT);
                    }else{
                        $number="u".str_pad($row[0]-1,4,"0",STR_PAD_LEFT);
                    }
                    query($db,"UPDATE `user` SET `number`='$number' WHERE `username`='$username'");
                    ?><script>alert("新增成功!");location.href="admin.php"</script><?php
                }
            }

            if(isset($_GET["edit"])){
                if($_GET["edit"]=="a0000"){
                    ?><script>alert("有人說你可以改網址嗎?????");location.href="admin.php"</script><?php
                }else{
                    $_SESSION["edit"]=$_GET["edit"];
                    ?><script>location.href="signupedit.php"</script><?php
                }
            }

            if(isset($_GET["del"])){
                if($_GET["del"]=="a0000"){
                    ?><script>alert("有人說你可以改網址嗎?????");location.href="admin.php"</script><?php
                }else{
                    $number=$_GET["del"];
                    query($db,"DELETE FROM `user` WHERE `number`='$number'");
                    ?><script>alert("刪除成功");location.href="admin.php"</script><?php
                }
            }

            if(isset($_GET["editsubmit"])){
                $number=$_GET["number"];
                $username=$_GET["username"];
                $password=$_GET["password"];
                $name=$_GET["name"];
                $row=query($db,"SELECT*FROM `user` WHERE `username`='$username'");
                if($number=="a0000"){
                    ?><script>alert("有人說你可以改編號嗎?????");location.href="admin.php"</script><?php
                }elseif($username==""||$password==""){
                    ?><script>alert("請填寫帳密!");location.href="signupedit.php"</script><?php
                }elseif($row&&$row[1]!=$number){
                    ?><script>alert("帳號已存在");location.href="signupedit.php"</script><?php
                }else{
                    if(isset($_GET["admin"])){
                        query($db,"UPDATE `user` SET `username`='$username',`password`='$password',`name`='$name',`permission`='管理者' WHERE `number`='$number'");
                    }else{
                        query($db,"UPDATE `user` SET `username`='$username',`password`='$password',`name`='$name',`permission`='一般使用者' WHERE `number`='$number'");
                    }
                    ?><script>alert("更改成功!");location.href="admin.php"</script><?php
                }
            }
        ?>
        <script src="logincheck.js"></script>
    </body>
</html>