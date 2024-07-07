<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>編輯問卷</title>
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="/chrisplugin/css/chrisplugin.css">
        <script src="error.js"></script>
        <script src="https://chrisplugin.pages.dev/js/chrisplugin.js"></script>
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){ header("location:index.php"); }
            $id=$_SESSION["id"];
            $row=query($db,"SELECT*FROM `question` WHERE `id`='$id'")[0];
            $id=$row[0];
            $coderow=query($db,"SELECT*FROM `questioncode` WHERE `questionid`='$id'");
            $userrow=query($db,"SELECT*FROM `user`");
        ?>
        <form method="POST">
            <div class="navigationbar">
                <div class="navigationbarleft"><div class="navigationbartitle">網路問卷管理系統-問卷邀請碼</div><br></div>
                <div class="navigationbarright">
                    <input type="submit" class="navigationbarbutton" name="goback" value="返回">
                    <input type="submit" class="navigationbarbutton" name="save" value="儲存">
                    <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
                </div>
            </div>
            <div class="main textcenter questioncodemaindiv macossectiondivy">
                <?php
                if(!isset($coderow)||@$coderow[0][2]==""){
                    if(@$_GET["key"]=="user"){
                        ?>
                        全體 <input type="radio" name="mod" class="radio" value="all">
                        單一用戶 <input type="radio" name="mod" class="radio" value="user" checked><br><br>
                        <table class="questioncodetable">
                            <?php
                                for($i=0;$i<count($userrow);$i=$i+1){
                                    ?>
                                    <tr>
                                        <td class="questioncodetd"><?php echo($userrow[$i][1]) ?></td>
                                        <td class="questioncodetd"><input type="text" name="code<?php echo($i) ?>" value=""></td>
                                    </tr>
                                    <?php
                                }
                            ?>
                        </table>
                        <?php
                    }else{
                        ?>
                        全體 <input type="radio" name="mod" class="radio" value="all" checked>
                        單一用戶 <input type="radio" name="mod" class="radio" value="user"><br><br>
                        <input type="text" name="code" value="<?php if(isset($coderow[0][3])){ echo($coderow[0][3]); } ?>">
                        <?php
                    }
                }else{
                    if(@$_GET["key"]=="all"){
                        ?>
                        全體 <input type="radio" name="mod" class="radio" value="all" checked>
                        單一用戶 <input type="radio" name="mod" class="radio" value="user"><br><br>
                        <input type="text" name="code" value="<?php if(isset($coderow[0][3])){ echo($coderow[0][3]); } ?>">
                        <?php
                    }else{
                        ?>
                        全體 <input type="radio" name="mod" class="radio" value="all">
                        單一用戶 <input type="radio" name="mod" class="radio" value="user" checked><br><br>
                        <table class="questioncodetable">
                            <?php
                            for($i=0;$i<count($userrow);$i=$i+1){
                                ?>
                                <tr>
                                    <td class="questioncodetd"><?php echo($userrow[$i][1]) ?></td>
                                    <td class="questioncodetd"><input type="text" name="code<?php echo($i) ?>" value="<?php
                                    for($j=0;$j<count($coderow);$j=$j+1){
                                        if(isset($coderow[$j][3])){
                                            if($coderow[$j][2]==$userrow[$i][1]){
                                                echo($coderow[$j][3]);
                                            }
                                        }
                                    }
                                    ?>"></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                        <?php
                    }
                }
                ?>
            </div>
        </form>
        <?php
            if(isset($_POST["newqust"])){
                $_SESSION["count"]=$_SESSION["count"]+1;
                ?><script>location.href="form.php"</script><?php
            }
            if(isset($_POST["save"])){
                query($db,"DELETE FROM `questioncode` WHERE `questionid`='$id'");
                if($_POST["mod"]=="all"){
                    $code=$_POST["code"];
                    $row=query($db,"SELECT*FROM `questioncode` WHERE `code`='$code'");
                    if($row){
                        ?><script>alert("邀請碼已存在")</script><?php
                    }else{
                        query($db,"INSERT INTO `questioncode`(`questionid`,`user`,`code`)VALUES('$id','','$code')");
                    }
                }elseif($_POST["mod"]=="user"){
                    for($i=0;$i<count($userrow);$i=$i+1){
                        $code=$_POST["code".$i];
                        $row=query($db,"SELECT*FROM `questioncode` WHERE `code`='$code'");
                        if($code!=""){
                            if(!$row||$row[$i][1]==$id){
                                query($db,"INSERT INTO `questioncode`(`questionid`,`user`,`code`)VALUES('$id',?,'$code')",[$userrow[$i][1]]);
                            }else{
                                ?><script>alert("邀請碼已存在錯誤user=><?php echo($userrow[$i][1]) ?>")</script><?php
                            }
                        }
                    }
                }else{ ?><script>e404();location.href="questioncode.php"</script><?php }
                ?><script>alert("儲存成功");location.href="form.php"</script><?php
            }
            if(isset($_POST["goback"])){
                ?><script>location.href="form.php?id=<?php echo($_SESSION["id"]) ?>"</script><?php
            }
        ?>
        <script src="questioncode.js"></script>
    </body>
</html>