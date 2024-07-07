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
        <?php
            include("link.php");
            if(!isset($_GET["edit"])){
                ?>
                <form>
                    <div class="main">
                        帳號: <input type="text" name="username"><br><br>
                        密碼: <input type="text" name="password"><br><br>
                        姓名: <input type="text" name="name"><br><br>
                        <select name="permission">
                            <option value="一般使用者">一般使用者</option>
                            <option value="管理者">管理者</option>
                        </select>
                        <input type="button" onclick="location.href='admin.php'" value="返回">
                        <input type="submit" name="new" value="送出">
                    </div>
                </form>
                <?php
            }else{
                $id=$_GET["edit"];
                $row=query($db,"SELECT*FROM `user` WHERE `id`=?",[$id])[0];
                $_SESSION["editid"]=$id;
                ?>
                <form>
                    <div class="main">
                        帳號: <input type="text" name="username" value="<?= $row[1] ?>" disabled><br><br>
                        密碼: <input type="text" name="password" value="<?= $row[2] ?>"><br><br>
                        姓名: <input type="text" name="name" value="<?= $row[3] ?>"><br><br>
                        <?php
                            if($row[5]=="一般使用者"){
                                ?>
                                <select name="permission">
                                    <option value="一般使用者">一般使用者</option>
                                    <option value="管理者">管理者</option>
                                </select><?php
                            }else{
                                ?>
                                <select name="permission">
                                    <option value="一般使用者">一般使用者</option>
                                    <option value="管理者" selected>管理者</option>
                                </select><?php
                            }

                        ?>
                        <input type="button" onclick="location.href='admin.php'" value="返回">
                        <input type="submit" name="editsubmit" value="送出">
                    </div>
                </form>
                <?php
            }

            if(isset($_GET["new"])){
                $username=$_GET["username"];
                $password=$_GET["password"];
                $name=$_GET["name"];
                $permission=$_GET["permission"];
                if(!$row=query($db,"SELECT*FROM `user` WHERE `username`=?",[$username])){
                    query($db,"INSERT INTO `user`(`username`,`password`,`name`,`number`,`permission`)VALUES(?,?,?,?,?)",[$username,$password,$name,"",$permission]);
                    $row=query($db,"SELECT*FROM `user` WHERE `username`=?",[$username])[0];
                    query($db,"UPDATE `user` SET `number`=? WHERE `id`=?",[str_pad($row[0]-1,5,"0",STR_PAD_LEFT),$row[0]]);
                    ?><script>alert("新增成功");location.href="admin.php"</script><?php
                }else{
                    ?><script>alert("帳號已被註冊");location.href="newedituser.php"</script><?php
                }
            }

            if(isset($_GET["editsubmit"])){
                $id=$_SESSION["editid"];
                $password=$_GET["password"];
                $name=$_GET["name"];
                $permission=$_GET["permission"];
                query($db,"UPDATE `user` SET `password`=?,`name`=?,`permission`=? WHERE `id`='$id'",[$password,$name,$permission]);
                ?><script>alert("修改成功");location.href="admin.php"</script><?php
            }

            if(isset($_GET["del"])){
                $id=$_GET["del"];
                if($id!="1"){
                    query($db,"DELETE FROM `user` WHERE `id`='$id'");
                    ?><script>alert("刪除成功");location.href="admin.php"</script><?php
                }
            }
        ?>
    </body>
</html>