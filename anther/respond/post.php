<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Document</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(isset($_POST["submit"])){
                $link=$_POST["link"];
                $question=$_POST["question"];
                $context=$_POST["context"];
                $email=$_POST["email"];
                $anthor=$_POST["anthor"];
                if($question=="none"){
                    ?><script>alert("請選擇問題");location.href="post.php"</script><?php
                }else{
                    query($db,"INSERT INTO `log`(`link`,`question`,`context`,`email`,`anthor`,`finish`,`posttime`)VALUES(?,?,?,?,?,?,?)",[$link,$question,$context,$email,$anthor,"no",$time]);
                    ?><script>location.href="post.php"</script><?php
                }
            }
        ?>
        <div class="main2">
            您的回覆已提交成功<br>
            請等待回復/修復<br>
            謝謝您的回報<br>
            <input type="button" class="reflashbutton" onclick="location.href='../../'" value="回到首頁">
            <input type="button" class="submitbutton" onclick="location.href='admin.php'" value="狀態">
        </div>
    </body>
</html>