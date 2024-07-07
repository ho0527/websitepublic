<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>signup</title>
        <link rel="stylesheet" href="/website/index.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])||$_SESSION["data"]!=1){ header("location:index.php"); }
            if(isset($_GET["edit"])){
                $id=$_GET["edit"];
                $_SESSION["id"]=$id;
                $row=query($db,"SELECT*FROM `user` WHERE `id`='$id'")[0];
                ?>
                <div class="navigationbar">
                    <div class="navigationbarleft">
                        <div class="navigationbartitle">專案討論系統</div>
                    </div>
                    <div class="navigationbarright">
                        <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='signupedit.php'" value="修改">
                        <input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="使用者管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='project.php'" value="專案管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='teamleader.php'" value="組長功能管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='statistic.php'" value="統計管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
                    </div>
                </div>
                <div class="main center loginmain">
                    <form method="POST">
                        <div class="inputdiv">
                            <div class="label">帳號</div>
                            <div class="input underline endicon">
                                <input type="text" name="username" value="<?php echo($row[1]) ?>">
                                <div class="icon"><img src="/website/material/icon/user.svg" class="iconinputicon" draggable="false"></div>
                            </div>
                        </div>
                        <div class="inputdiv">
                            <div class="label">密碼</div>
                            <div class="input underline endicon">
                                <input type="text" name="password" value="<?php echo($row[2]) ?>">
                                <div class="icon"><img src="/website/material/icon/lock.svg" class="iconinputicon cursor_pointer" id="passwordicon" draggable="false"></div>
                            </div>
                        </div>
                        <div class="inputdiv">
                            <div class="label">姓名</div>
                            <div class="input underline endicon">
                                <input type="text" name="name" value="<?php echo($row[3]) ?>">
                                <div class="icon"><img src="/website/material/icon/user.svg" class="iconinputicon" draggable="false"></div>
                            </div>
                        </div>
                        <div class="textcenter">
                            <input type="button" class="button outline" onclick="location.href='admin.php'" value="返回">
                            <input type="submit" class="button outline" name="edit" value="修改">
                        </div>
                    </form>
                </div>
                <?php
            }else{
                ?>
                <div class="navigationbar">
                    <div class="navigationbarleft">
                        <div class="navigationbartitle">專案討論系統</div>
                    </div>
                    <div class="navigationbarright">
                        <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='signupedit.php'" value="新增">
                        <input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="使用者管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='project.php'" value="專案管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='teamleader.php'" value="組長功能管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='statistic.php'" value="統計管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
                    </div>
                </div>
                <div class="main center loginmain">
                    <form method="POST">
                        <div class="inputdiv">
                            <div class="label">帳號</div>
                            <div class="input underline endicon">
                                <input type="text" name="username">
                                <div class="icon"><img src="/website/material/icon/user.svg" class="iconinputicon" draggable="false"></div>
                            </div>
                        </div>
                        <div class="inputdiv">
                            <div class="label">密碼</div>
                            <div class="input underline endicon">
                                <input type="text" name="password">
                                <div class="icon"><img src="/website/material/icon/lock.svg" class="iconinputicon cursor_pointer" id="passwordicon" draggable="false"></div>
                            </div>
                        </div>
                        <div class="inputdiv">
                            <div class="label">姓名</div>
                            <div class="input underline endicon">
                                <input type="text" name="name">
                                <div class="icon"><img src="/website/material/icon/user.svg" class="iconinputicon" draggable="false"></div>
                            </div>
                        </div>
                        <div class="textcenter">
                            <input type="button" class="button outline" onclick="location.href='admin.php'" value="返回">
                            <input type="submit" class="button is-secondary outline" name="signup" value="註冊">
                        </div>
                    </form>
                </div>
                <?php
            }
        ?>
        <?php
            $data=$_SESSION["data"];
            if(isset($_POST["signup"])){
                $username=$_POST["username"];
                $password=$_POST["password"];
                $name=$_POST["name"];
                $row=query($db,"SELECT*FROM `user` WHERE `username`='$username'")[0];
                if($row){
                    ?><script>alert("帳號已被註冊");location.href="signupedit.php"</script><?php
                }elseif($username==""||$password==""){
                    ?><script>alert("請輸入帳密");location.href="signupedit.php"</script><?php
                }else{
                    query($db,"INSERT INTO `user`(`username`,`password`,`name`)VALUES(?,?,?)",[$username,$password,$name]);
                    $row=query($db,"SELECT*FROM `user` WHERE `username`=?",[$username])[0];
                    query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$data,"新增使用者",$time,""]);
                    ?><script>alert("新增成功");location.href="admin.php"</script><?php
                }
            }

            if(isset($_POST["edit"])){
                $id=$_SESSION["id"];
                $username=$_POST["username"];
                $password=$_POST["password"];
                $name=$_POST["name"];
                $row=query($db,"SELECT*FROM `user` WHERE `username`='$username'")[0];
                if($row&&$row[0]!=$id){
                    ?><script>alert("帳號已被註冊");location.href="signupedit.php"</script><?php
                }elseif($username==""||$password==""){
                    ?><script>alert("請輸入帳密");location.href="signupedit.php"</script><?php
                }else{
                    query($db,"UPDATE`user`SET `username`=?,`password`=?,`name`=? WHERE `id`='$id'",[$username,$password,$name]);
                    $row=query($db,"SELECT*FROM `user` WHERE `username`=?",[$username])[0];
                    query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$data,"編輯使用者",$time,""]);
                    ?><script>alert("修改成功");location.href="admin.php"</script><?php
                }
            }

            if(isset($_GET["del"])){
                $id=$_GET["del"];
                if($row=query($db,"SELECT*FROM `user` WHERE `id`='$id'")[0]){
                    query($db,"DELETE FROM `user` WHERE `id`='$id'");
                    query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$data,"刪除使用者",$time,""]);
                    ?><script>alert("刪除成功!");location.href="admin.php"</script><?php
                }else{ ?><script>alert("帳號已被刪除!");location.href="admin.php"</script><?php }
            }
        ?>
    </body>
</html>