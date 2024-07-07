<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Shanghai Battle!</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <img src="banner.png" class="banner">
        <div class="navigationbar">
            <img src="logo.png" class="logo">
            <div class="navigationbarbuttondiv">
                <input type="button" class="navigationbarbutton" onclick="location.href='index.php'" value="玩家留言">
                <input type="button" class="navigationbarbutton selectbutton" onclick="location.href='post.php'" value="玩家參賽">
                <input type="button" class="navigationbarbutton" onclick="location.href='login.php'" value="網站管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='link.php?logout='" value="登出">
            </div>
        </div>
        <div class="main">
            <form method="POST" enctype="multipart/form-data">
                <div class="indextitle">玩家參賽畫面</div>
                <hr>
                姓&nbsp&nbsp名: <input type="text" class="input" name="username" value="<?= @$_SESSION["name"] ?>"><br><br>
                email: <input type="text" class="input" name="email" placeholder="要有@及一個以上的." value="<?= @$_SESSION["email"] ?>"><br><br>
                電&nbsp&nbsp話: <input type="text" class="input" name="tel" placeholder="只能包含數字或-" value="<?= @$_SESSION["tel"] ?>"><br><br>
                <input type="button" class="postbutton button" onclick="filechoose('file')" value="上傳頭像">
                <input type="button" class="postbutton button" onclick="location.href='post.php'" value="重設">
                <input type="submit" class="postbutton button" name="submit" value="參賽">
                <input type="file" class="inputfile" id="file" name="picture" accept="image/*">
            </form>
        </div>
        <?php
            include("link.php");
            if(isset($_POST["submit"])){
                $_SESSION["name"]=$_POST["username"];
                $_SESSION["email"]=$_POST["email"];
                $_SESSION["tel"]=$_POST["tel"];
                $username=$_SESSION["name"];
                $email=$_SESSION["email"];
                $tel=$_SESSION["tel"];
                if(!preg_match("/^.+\@.+\..+((\..+)+)?$/",$email)) {
                    ?><script>alert("email驗證失敗!");location.href="post.php"</script><?php
                }elseif(!preg_match("/^[0-9]+(\-[0-9]+)?$/",$tel)){
                    ?><script>alert("電話驗證失敗!");location.href="post.php"</script><?php
                }elseif($username==""){
                    ?><script>alert("請輸入名字!");location.href="post.php"</script><?php
                }else{
                    $picture="";
                    if(isset($_FILES["picture"]["name"])){
                        move_uploaded_file($_FILES["picture"]["tmp_name"],"image/".$_FILES["picture"]["name"]);
                        $picture="image/".$_FILES["picture"]["name"];
                    }
                    query($db,"INSERT INTO `comp`(`username`,`email`,`tel`,`picture`)VALUES(?,?,?,?)",[$username,$email,$tel,$picture]);
                    ?><script>alert("新增成功!");location.href="post.php"</script><?php
                    unset($_SESSION["name"]);
                    unset($_SESSION["email"]);
                    unset($_SESSION["tel"]);
                    unset($_SESSION["message"]);
                    unset($_SESSION["sn"]);
                }
            }
        ?>
        <script src="index.js"></script>
    </body>
</html>