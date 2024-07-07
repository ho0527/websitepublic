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
        ?>
        <div class="nav tr" id="nav"></div>

        <div class="main">
            <form method="POST">
                <p>
                    訪客姓名: <input type="text" name="name" value="<?= @$_SESSION["name"] ?>">
                </p>
                <P>
                    Email: <input type="text" name="email" value="<?= @$_SESSION["email"] ?>">
                    <label>
                        <?php
                            if(@$_SESSION["emailshow"]=="false"){
                                ?>顯示<input type="checkbox" name="emailshow" id=""><?php
                            }else{
                                ?>顯示<input type="checkbox" name="emailshow" id="" checked><?php
                            }
                        ?>
                    </label>
                </P>
                <P>
                    連絡電話: <input type="text" name="phone" value="<?= @$_SESSION["phone"] ?>">
                    <label>
                        <?php
                            if(@$_SESSION["phoneshow"]=="false"){
                                ?>顯示<input type="checkbox" name="phoneshow" id=""><?php
                            }else{
                                ?>顯示<input type="checkbox" name="phoneshow" id="" checked><?php
                            }
                        ?>
                    </label>
                </P>
                <P>
                    <textarea class="fill" name="content" placeholder="留言內容"><?= @$_SESSION["content"] ?></textarea>
                </P>
                <P>
                    留言序號: <input type="number" name="code" value="<?= @$_SESSION["code"] ?>">
                </P>
                <p>
                    <input type="button" onclick="document.getElementById('file').click()" value="上傳圖片">
                    <input type="button" onclick="location.href='?reset='" value="重設">
                    <input type="submit" name="submit" value="送出">
                </p>
                <input type="file" class="hidden" id="file">
                <input type="hidden" id="filetext" name="file" value="<?= @$_SESSION["file"] ?>">
            </form>
        </div>

        <?php
            if(isset($_GET["reset"])){
                unset($_SESSION["name"]);
                unset($_SESSION["email"]);
                unset($_SESSION["emailshow"]);
                unset($_SESSION["phone"]);
                unset($_SESSION["phoneshow"]);
                unset($_SESSION["content"]);
                unset($_SESSION["code"]);
                unset($_SESSION["file"]);
                header("location:newcomment.php");
            }
            if(isset($_POST["submit"])){
                $emailshow="false";
                $phoneshow="false";
                $phonecheck=false;
                if(isset($_POST["emailshow"])){ $emailshow="true"; }
                if(isset($_POST["phoneshow"])){ $phoneshow="true"; }

                if(preg_match("/^[0-9]{2}\-[0-9]{4}\-[0-9]{4}$/",$_POST["phone"])){
                    $phonecheck=true;
                }elseif(preg_match("/^[0-9]{4}\-[0-9]{6}$/",$_POST["phone"])){
                    $phonecheck=true;
                }elseif(preg_match("/^[0-9]{3}\-[0-9]{4}\-[0-9]{3}$/",$_POST["phone"])){
                    $phonecheck=true;
                }

                if(!preg_match("/(\.(.+)?\@)|(\@(.+)?\.)/",$_POST["email"])){
                    $_SESSION["name"]=$_POST["name"];
                    $_SESSION["email"]=$_POST["email"];
                    $_SESSION["emailshow"]=$emailshow;
                    $_SESSION["phone"]=$_POST["phone"];
                    $_SESSION["phoneshow"]=$phoneshow;
                    $_SESSION["content"]=$_POST["content"];
                    $_SESSION["code"]=$_POST["code"];
                    $_SESSION["file"]=$_POST["file"];
                    ?><script>alert("email輸入錯誤");location.href="newcomment.php"</script><?php
                }elseif(!$phonecheck){
                    $_SESSION["name"]=$_POST["name"];
                    $_SESSION["email"]=$_POST["email"];
                    $_SESSION["emailshow"]=$emailshow;
                    $_SESSION["phone"]=$_POST["phone"];
                    $_SESSION["phoneshow"]=$phoneshow;
                    $_SESSION["content"]=$_POST["content"];
                    $_SESSION["code"]=$_POST["code"];
                    $_SESSION["file"]=$_POST["file"];
                    ?><script>alert("電話輸入錯誤");location.href="newcomment.php"</script><?php
                }elseif(!preg_match("/^[0-9]{4}$/",$_POST["code"])){
                    $_SESSION["name"]=$_POST["name"];
                    $_SESSION["email"]=$_POST["email"];
                    $_SESSION["emailshow"]=$emailshow;
                    $_SESSION["phone"]=$_POST["phone"];
                    $_SESSION["phoneshow"]=$phoneshow;
                    $_SESSION["content"]=$_POST["content"];
                    $_SESSION["code"]=$_POST["code"];
                    $_SESSION["file"]=$_POST["file"];
                    ?><script>alert("序號輸入錯誤");location.href="newcomment.php"</script><?php
                }else{
                    unset($_SESSION["name"]);
                    unset($_SESSION["email"]);
                    unset($_SESSION["emailshow"]);
                    unset($_SESSION["phone"]);
                    unset($_SESSION["phoneshow"]);
                    unset($_SESSION["content"]);
                    unset($_SESSION["code"]);
                    unset($_SESSION["file"]);
                    query($db,"INSERT INTO `comment`VALUE(?,?,?,?,?,?,?,?,?,?,?,?,?,?)",["",$_POST["file"],$_POST["name"],$_POST["content"],$_POST["email"],$emailshow,$_POST["phone"],$phoneshow,$_POST["code"],"","",$time,"",""]);
                    ?><script>alert("新增成功");location.href="comment.php"</script><?php
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