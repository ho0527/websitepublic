<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>重設帳密</title>
        <link href="index.css" rel="Stylesheet">
    </head>
    <body>
        <div class="signupdiv">
            <form>
                <?php
                    include("link.php");
                    if(isset($_GET["number"])){
                        $number=$_GET["number"];
                        $user=mysqli_query($db,"SELECT*FROM `user` WHERE `userNumber`='$number'");
                        $admin=mysqli_query($db,"SELECT*FROM `admin` WHERE `adminNumber`='$number'");
                        if($row=mysqli_fetch_row($user)){
                            ?>
                            <from class="text">
                                編號: <input type="text" name="number" value="<?php echo($number); ?>" readonly><br><br>
                                帳號: <input type="text" name="username" value="<?php echo($row[1]); ?>"><br><br>
                                用戶名: <input type="text" name="name" value="<?php echo($row[3]); ?>"><br><br>
                                密碼: <input type="text" name="code" value="<?php echo($row[2]); ?>"><br><br>
                                <button name="enter">更改帳號</button>
                            </from>
                            <?php
                        }elseif($row=mysqli_fetch_row($admin)){
                            ?>
                            <from>
                                編號: <input type="text" name="number" value="<?php echo($number); ?>" readonly><br><br>
                                帳號: <input type="text" name="username" value="<?php echo($row[1]); ?>"><br><br>
                                用戶名: <input type="text" name="name" value="<?php echo($row[3]); ?>"><br><br>
                                密碼: <input type="text" name="code" value="<?php echo($row[2]); ?>"><br><br>
                                <button name="enter">更改帳號</button>
                            </from>
                            <?php
                        }else{
                            ?><script>alert("帳號已被刪除!");location.href="adminWelcome.php"</script><?php
                        }
                    }
                ?>
                <button type="button" id="go_back" onclick="location.href='adminWelcome.php'">返回主頁</button>
            </form>
        </div>
        <?php
            if(isset($_GET["enter"])){
                $username=$_GET["username"];
                $code=$_GET["code"];
                $name=$_GET["name"];
                $user=mysqli_query($db,"SELECT*FROM `user` WHERE `userNumber`='$number'");
                $admin=mysqli_query($db,"SELECT*FROM `admin` WHERE `adminNumber `='$number'");
                if($username==""&&$code==""){
                    ?><script>alert("請填寫帳密!");location.href="adminWelcome.php"</script><?php
                }elseif($row&&$row[0]!=$number){
                    ?><script>alert("帳號已存在");location.href="adminWelcome.php"</script><?php
                }else{
                    if($row=mysqli_fetch_row($user)){
                        mysqli_query($db,"UPDATE `user` SET `name`='$name',`userCode`='$code',`userName`='$username' WHERE `userNumber`='$number'");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `user` WHERE `userNumber`='$number'"));
                        mysqli_query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)
                        VALUES('$number','$row[1]','$row[2]','$row[3]','一般使用者','-','-','管理員編輯','$time')");
                        ?><script>alert("更改成功!");location.href="adminWelcome.php"</script><?php
                    }elseif($row=mysqli_fetch_row($admin)){
                        mysqli_query($db,"UPDATE `admin` SET `name`='$name',`adminCode`='$code',`adminName`='$username' WHERE `adminnumber`='$number'");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `admin` WHERE `adminNumber`='$number'"));
                        mysqli_query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`move`,`movetime`)VALUES('$number','$row[1]','$row[2]','$row[3]','管理者','管理員編輯','$time')");
                        ?><script>alert("更改成功!");location.href="adminWelcome.php"</script><?php
                    }
                }
            }
        ?>
    </body>
</html>