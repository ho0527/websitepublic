<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>setting</title>
        <link href="index.css" rel="Stylesheet">
    </head>
    <body>
        <button id="go_back" onclick="location.href='userWelcome.php'">返回</button><br><br>
        <button id="change-acc" onclick="location.href='changeACC.php'">更改帳號密碼</button><br><br>
        <button id="del-acc">刪除帳號</button>
        <form>
            <div id="del-acc-form">
            確定刪除?<br>
            <button id="cancle">取消</button>
            <button id="comform" name="comform">確定</button>
            </div>
        </form>
        <script src="setting.js"></script>
        <?php
            include("link.php");
            if(isset($_GET["comform"])){
                @$user_data=$_SESSION["data"];
                $user=mysqli_query($db,"SELECT*FROM `user` WHERE `userNumber`='$user_data'");
                if($row=mysqli_fetch_row($user)){
                    $del=mysqli_query($db,"DELETE FROM `user` WHERE `userNumber`='$user_data'");
                    mysqli_query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)
                    VALUES('$user_data','$row[1]','$row[2]','$row[3]','一般使用者','-','-','用戶刪除','$time')");
                    ?><script>alert("刪除成功");location.href="index.php"</script><?php
                }else{
                    echo("請先登入");
                }
            }
        ?>
    </body>
</html>