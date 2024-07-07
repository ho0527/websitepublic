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
            $row=query($db,"SELECT*FROM `comment` WHERE `id`=?",[$_GET["id"]])[0];
        ?>
        <div class="nav tr" id="nav"></div>

        <div class="main">
            <form method="POST">
                <p>
                    訪客姓名: <input type="text" name="name" value="<?= @$row["name"] ?>">
                </p>
                <P>
                    Email: <input type="text" name="email" value="<?= @$row["email"] ?>">
                    <label>
                        <?php
                            if(@$row["emailshow"]=="false"){
                                ?>顯示<input type="checkbox" name="emailshow" id=""><?php
                            }else{
                                ?>顯示<input type="checkbox" name="emailshow" id="" checked><?php
                            }
                        ?>
                    </label>
                </P>
                <P>
                    連絡電話: <input type="text" name="phone" value="<?= @$row["phone"] ?>">
                    <label>
                        <?php
                            if(@$row["phoneshow"]=="false"){
                                ?>顯示<input type="checkbox" name="phoneshow" id=""><?php
                            }else{
                                ?>顯示<input type="checkbox" name="phoneshow" id="" checked><?php
                            }
                        ?>
                    </label>
                </P>
                <P>
                    <textarea class="fill" name="content" placeholder="留言內容"><?= @$row["content"] ?></textarea>
                </P>
                <p>
                    <input type="button" onclick="document.getElementById('file').click()" value="上傳圖片">
                    <input type="reset" value="重設">
                    <input type="submit" name="submit" value="送出">
                </p>
                <input type="file" class="hidden" id="file">
                <input type="hidden" id="filetext" name="file" value="<?= @$row["image"] ?>">
                <input type="hidden" id="" name="id" value="<?= @$_GET["id"] ?>">
            </form>
        </div>

        <?php
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
                    ?><script>alert("email輸入錯誤");location.href="editcomment.php?id=<?= $_POST["id"] ?>"</script><?php
                }elseif(!$phonecheck){
                    ?><script>alert("電話輸入錯誤");location.href="editcomment.php?id=<?= $_POST["id"] ?>"</script><?php
                }else{
                    query($db,"UPDATE `comment` SET `image`=?,`name`=?,`content`=?,`email`=?,`emailshow`=?,`phone`=?,`phoneshow`=?,`updatetime`=? WHERE `id`=?",[$_POST["file"],$_POST["name"],$_POST["content"],$_POST["email"],$emailshow,$_POST["phone"],$phoneshow,$time,$_POST["id"]]);
                    ?><script>alert("修改成功");location.href="comment.php"</script><?php
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