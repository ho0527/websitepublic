<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>管理者專區</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <table>
            <tr>
                <td>
                    <h1>管理者專區</h1>
                    <input type="button" onclick="location.href='signup.php'" value="註冊">
                    <input type="button" onclick="location.href='index.php'" value="登出">
                </td>
                <td style="text-align: right;">
                    <form>
                        <input type="text" name="serch"><br>
                        <input type="submit" name="enter">
                    </form>
                </td>
            </tr>
            <tr>
                <table>
                    <form>
                        <tr>
                            <td class="adminnum">編號<input type="button" id="num"></td>
                            <td class="admin">帳號<input type="button" id="user"></td>
                            <td class="admin">密碼<input type="button" id="password"></td>
                            <td class="admin">名字<input type="button" id="uname"></td>
                            <td class="admin">權限</td>
                            <td class="admin">登入時間</td>
                            <td class="admin">登出時間</td>
                            <td class="admin">動作</td>
                            <td class="admin">動作時間</td>
                            <input type="button" value="確定(升降冪)" onclick="enter()">
                        </tr>
                        <?php
                            include("link.php");
                            include("admindef.php");
                            if(isset($_GET["submit"])){
                                $text=$_GET["serch"];
                                mysqli_query($db,"SELECT*FROM `data` WHERE `username`LIKE'%$text%'");
                            }
                            if(isset($_GET["enter"])){
                                if($_GET["num"]=="降冪"){
                                    
                                }
                            }else{
                                uper($db,1);
                            }
                            if(isset($_GET["del"])){
                                $acc=$_GET["del"];
                                echo($acc);
                                $udata=mysqli_query($db,"SELECT*FROM `user` WHERE `usernumber`='$acc'");
                                $adata=mysqli_query($db,"SELECT*FROM `admin` WHERE `usernumber`='$acc'");
                                if($row=mysqli_fetch_row($udata)){
                                    mysqli_query($db,"DELETE FROM `user` WHERE `usernumber`='$acc'");
                                    mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`,`permission`,`logintime`, `logouttime`, `move`, `movetime`)
                                    VALUES('$row[1]','$row[2]','$row[3]','$row[4]','一般使用者','$date','-','admindel','$date')");
                                    ?><script>alert("刪除成功");location.href="adminWelcome.php"</script><?php
                                }elseif($row=mysqli_fetch_row($adata)){
                                    mysqli_query($db,"DELETE FROM `admin` WHERE `usernumber`='$acc'");
                                    mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`,`permission`,`logintime`, `logouttime`, `move`, `movetime`)
                                    VALUES('$row[1]','$row[2]','$row[3]','$row[4]','管理者','$date','-','admindel','$date')");
                                    ?><script>alert("刪除成功");location.href="adminWelcome.php"</script><?php
                                }else{
                                    ?><script>alert("帳號已被刪除");location.href="adminWelcome.php"</script><?php
                                }
                            }
                        ?>
                    </form>
                </table>
            </tr>
        </table>
        <script src="admin.js"></script>
    </body>
</html>