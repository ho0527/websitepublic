<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>signup</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <div class="main">
            <form>
                帳號: <input type="text" class="input" name="username"><br><br>
                密碼: <input type="text" class="input" name="code"><br><br>
                名稱: <input type="text" class="input" name="name"><br><br>
                管理員權限: <input type="checkbox" class="checkbox" name="adminbox">
                <input type="button" onclick="location.href='admin.php'" class="button" value="返回">
                <input type="submit" class="button" value="送出"><br>
            </form>
        </div>
    <?php
        include("link.php");
        if(isset($_GET["username"])){
            $username=$_GET["username"];
            $name=$_GET["name"];
            $code=$_GET["code"];
            $rowuser=query($db,"SELECT*FROM `user` WHERE `username`='$username'");
            $rowadmin=query($db,"SELECT*FROM `admin` WHERE `adminname`='$username'");
            if($rowuser||$rowadmin){
                echo("帳號已被註冊");
            }elseif($username==""||$code==""){
                echo("請輸入帳密");
            }else{
                $rowuser=$rowuser[0];
                $rowadmin=$rowadmin[0];
                if(isset($_GET["adminbox"])){
                    query($db,"INSERT INTO `admin`(`adminname`,`admincode`,`name`)VALUES('$username','$code','$name')");
                    $row=query($db,"SELECT*FROM `admin` WHERE `adminname`='$username'")[0];
                    $number="a".str_pad($row[0],3,"0",STR_PAD_LEFT);
                    query($db,"UPDATE `admin` SET `adminNumber`='$number' WHERE `adminname`='$username'");
                    $row2=query($db,"SELECT*FROM `admin` WHERE `adminname`='$username'")[0];
                    query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)VALUES('$row2[4]','$row[1]','$row[2]','$row[3]','管理者','-','-','註冊','$time')");
                    ?><script>alert("新增成功");location.href="admin.php"</script><?php
                }else{
                    query($db,"INSERT INTO `user`(`username`,`usercode`,`name`)VALUES('$username','$code','$name')");
                    $row=query($db,"SELECT*FROM `user` WHERE `username`='$username'")[0];
                    $number="u".str_pad($row[0],3,"0",STR_PAD_LEFT);
                    query($db,"UPDATE user SET `userNumber`='$number' WHERE `username`='$username'");
                    $row2=query($db,"SELECT*FROM `user` WHERE `username`='$username'")[0];
                    query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)VALUES('$row2[4]','$row[1]','$row[2]','$row[3]','一般使用者','-','-','註冊','$time')");
                    ?><script>alert("新增成功");location.href="admin.php"</script><?php
                }
            }
        }
    ?>
    </body>
</html>