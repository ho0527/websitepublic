<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>快樂旅遊網</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            $row=query($db,"SELECT*FROM `comment` WHERE `id`=?",[$_GET["editcomment"]])[0];
        ?>
        <div class="nav tr" id="nav"></div>

        <div class="main">
            <form method="POST">
                <p>帳號: <input type="text" name="username" value="<?= @$row["username"] ?>"></p>
                <p>
                    Email: <input type="text" name="email" value="<?= @$row["email"] ?>">
                    顯示<input type="checkbox" name="emailshow" checked>
                </p>
                <p>
                    聯絡電話: <input type="text" name="phone" value="<?= @$row["phone"] ?>">
                    顯示<input type="checkbox" name="phoneshow" checked>
                </p>
                <p>
                    <textarea class="fill" name="content" placeholder="內容"><?= @$row["content"] ?></textarea>
                </p>
                <p class="tc">
                    <input type="button" onclick="document.getElementById('file').click()" value="上傳圖片">
                    <input type="button" onclick="location.reload()" value="重設">
                    <input type="submit" name="submit" value="送出">
                </p>
                <input type="file" class="hidden" id="file" accept="image/*">
                <input type="hidden" id="filetext" name="image" value="<?= @$row["image"] ?>">
                <input type="hidden" id="" name="id" value="<?= @$_GET["editcomment"] ?>">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                $emailshow="false";
                $phoneshow="false";

                if(isset($_POST["emailshow"])){
                    $emailshow="true";
                }

                if(isset($_POST["phoneshow"])){
                    $phoneshow="true";
                }

                if(!preg_match("/^.+\@.+\..+((\..+)+)?$/",$_POST["email"])){
                    $_SESSION["username"]=$_POST["username"];
                    $_SESSION["email"]=$_POST["email"];
                    $_SESSION["phone"]=$_POST["phone"];
                    $_SESSION["content"]=$_POST["content"];
                    $_SESSION["image"]=$_POST["image"];
                    ?><script>alert("email輸入錯誤");location.href="newcomment.php"</script><?php
                }elseif(!preg_match("/^[0-9]+((\-[0-9]+)+)?$/",$_POST["phone"])){
                    $_SESSION["username"]=$_POST["username"];
                    $_SESSION["email"]=$_POST["email"];
                    $_SESSION["phone"]=$_POST["phone"];
                    $_SESSION["content"]=$_POST["content"];
                    $_SESSION["image"]=$_POST["image"];
                    ?><script>alert("phone輸入錯誤");location.href="newcomment.php"</script><?php
                }else{
                    unset($_SESSION["username"]);
                    unset($_SESSION["email"]);
                    unset($_SESSION["phone"]);
                    unset($_SESSION["content"]);
                    unset($_SESSION["image"]);
                    query($db,"UPDATE `comment` SET `image`=?,`username`=?,`content`=?,`email`=?,`emailshow`=?,`phone`=?,`phoneshow`=?,`updatetime`=? WHERE `id`=?",[$_POST["image"],$_POST["username"],$_POST["content"],$_POST["email"],$emailshow,$_POST["phone"],$phoneshow,$time,$_POST["id"]]);
                    ?><script>alert("上傳成功");location.href="comment.php"</script><?php
                }
            }
        ?>

        <script src="init.js"></script>
        <script>
            document.getElementById("file").onchange=function(event){
                let file=event.target.files[0]
                let filereader=new FileReader()

                filereader.onload=function(){
                    document.getElementById("filetext").value=filereader.result
                }

                filereader.readAsDataURL(file)
            }
        </script>
    </body>
</html>