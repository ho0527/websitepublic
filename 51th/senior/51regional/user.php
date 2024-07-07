<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>編輯問卷</title>
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="/chrisplugin/css/chrisplugin.css">
        <script src="error.js"></script>
        <script src="userdef.js"></script>
        <script src="https://chrisplugin.pages.dev/js/chrisplugin.js"></script>
    </head>
    <body id="body">
        <?php
            include("link.php");
            if(!isset($_SESSION["id"])){ header("location:index.php"); }
            $id=$_SESSION["id"];
            $userid="";
            if(isset($_SESSION["data"])){
                $userid=$_SESSION["data"];
            }
            $row=query($db,"SELECT*FROM `question` WHERE `id`='$id'")[0];
            if($row[4]>=$row[6]&&$row[6]!=""){ ?><script>user("您好~!本問卷已達所需之數量，感謝您的支持")</script><?php }
            elseif(!query($db,"SELECT*FROM `questioncode` WHERE `questionid`='$id'")){ ?><script>user("本問卷尚未開放，感謝您的支持")</script><?php }
            else{ ?><script>user("true")</script><?php }
        ?>
        <script>
            let userid="<?php echo($userid) ?>";
            let row=<?php echo(json_encode($row)) ?>;
            let questionrow=JSON.parse(<?php echo(json_encode($row[7])) ?>)
        </script>
        <script src="user.js"></script>
    </body>
</html>