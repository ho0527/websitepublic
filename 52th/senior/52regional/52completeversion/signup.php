<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>signup</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <div class="signupdiv">
            <form>
                用戶帳號: <input type="text" class="input" name="username"><br><br>
                密碼: <input type="text" class="input" name="code"><br><br>
                用戶名: <input type="text" class="input" name="name"><br><br>
                管理員權限: <input type="checkbox" name="adminbox"><br><br>
                <input type="submit" class="button" value="送出">
                <input type="button" onclick="location.href='adminWelcome.php'" class="button" value="返回"><br>
            </form>
        </div>
    <?php
        include("link.php");
        if(isset($_GET["username"])){
            $username=$_GET["username"];
            $name=$_GET["name"];
            $code=$_GET["code"];
            $user=mysqli_query($db,"SELECT*FROM `user` WHERE userName='$username'");
            $admin=mysqli_query($db,"SELECT*FROM `admin` WHERE adminName='$username'");
            $rowuser=mysqli_fetch_row($user);
            $rowadmin=mysqli_fetch_row($admin);
            if($rowuser||$rowadmin){
                echo("帳號已被註冊");
            }else if($username==""||$code==""){
                echo("請輸入帳密");
            }else{
                if(isset($_GET["adminbox"])){
                    mysqli_query($db,"INSERT INTO `admin`(`adminName`,`adminCode`,`name`)VALUES('$username','$code','$name')");
                    $userdata=mysqli_query($db,"SELECT*FROM `admin` WHERE `adminName`='$username'");
                    $row=mysqli_fetch_row($userdata);
                    $number="a".str_pad($row[0],3,"0",STR_PAD_LEFT);
                    mysqli_query($db,"UPDATE `admin` SET `adminNumber`='$number' WHERE `adminName`='$username'");
                    $userdata2=mysqli_query($db,"SELECT*FROM `admin` WHERE `adminName`='$username'");
                    $row2=mysqli_fetch_row($userdata2);
                    mysqli_query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)
                    VALUES('$row2[4]','$row[1]','$row[2]','$row[3]','管理者','-','-','註冊','$time')");
                    header("Location:adminWelcome.php");
                }else{
                    mysqli_query($db,"INSERT INTO `user`(`userName`,`userCode`,`name`)VALUES('$username','$code','$name')");
                    $userdata=mysqli_query($db,"SELECT*FROM `user` WHERE `userName`='$username'");
                    $row=mysqli_fetch_row($userdata);
                    $number="u".str_pad($row[0],3,"0",STR_PAD_LEFT);
                    mysqli_query($db,"UPDATE user SET `userNumber`='$number' WHERE `userName`='$username'");
                    $userdata=mysqli_query($db,"SELECT*FROM `user` WHERE `userName`='$username'");
                    $row2=mysqli_fetch_row($userdata);
                    mysqli_query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)
                    VALUES('$row2[4]','$row[1]','$row[2]','$row[3]','一般使用者','-','-','註冊','$time')");
                    header("Location:adminWelcome.php");
                }
            }
        }
    ?>
    </body>
</html>