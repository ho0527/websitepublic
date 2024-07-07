<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>signup</title>
        <link rel="stylesheet" href="/index.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){ header("location:index.php"); }
            if(!isset($_GET["id"])){ header("location:project.php"); }
            $_SESSION["id"]=$_GET["id"];
            if(isset($_GET["edit"])){
                $id=$_GET["edit"];
                $_SESSION["scoreindexid"]=$id;
                $row=query($db,"SELECT*FROM `scoreindex` WHERE `id`='$id'")[0];
                ?>
                <div class="navigationbar">
                    <div class="navigationbarleft">
                        <div class="navigationbartitle">專案討論系統</div>
                    </div>
                    <div class="navigationbarright">
                        <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='neweditscoreindex.php'" value="修改評分指標">
                        <?php
                            if($_SESSION["data"]==1){ ?><input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="使用者管理"><?php }
                        ?>
                        <input type="button" class="navigationbarbutton" onclick="location.href='project.php'" value="專案管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='teamleader.php'" value="組長功能管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='statistic.php'" value="統計管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
                    </div>
                </div>
                <div class="main center">
                    <form method="POST">
                        <div class="inputmargin">
                            <div class="sttext">指標</div>
                            <div class="stinput underline">
                                <input type="text" class="input" name="index" value="<?php echo($row[2]); ?>">
                            </div>
                        </div>
                        <div class="textcenter">
                            <input type="button" class="stbutton outline" onclick="location.href='scoreindex.php?id=<?php echo($_GET['id']) ?>'" value="返回">
                            <input type="submit" class="stbutton outline" name="edit" value="送出"><br>
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
                        <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='neweditscoreindex.php'" value="新增評分指標">
                        <?php
                            if($_SESSION["data"]==1){ ?><input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="使用者管理"><?php }
                        ?>
                        <input type="button" class="navigationbarbutton" onclick="location.href='project.php'" value="專案管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='teamleader.php'" value="組長功能管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='statistic.php'" value="統計管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
                    </div>
                </div>
                <div class="main center">
                    <form method="POST">
                        <div class="inputmargin">
                            <div class="sttext">指標</div>
                            <div class="stinput underline">
                                <input type="text" class="input" name="index">
                            </div>
                        </div>
                        <div class="textcenter">
                            <input type="button" class="stbutton outline" onclick="location.href='scoreindex.php?id=<?php echo($_GET['id']) ?>'" value="返回">
                            <input type="submit" class="stbutton outline" name="new" value="送出"><br>
                        </div>
                    </form>
                </div>
                <?php
            }
        ?>
    <?php
        $data=$_SESSION["data"];
        if(isset($_POST["new"])){
            $index=$_POST["index"];
            $id=$_SESSION["id"];
            $row=query($db,"SELECT*FROM `scoreindex` WHERE `index`='$index'AND`project_facingid`='$id'")[0];
            if($row){
                ?><script>alert("帳號已被註冊");location.href="neweditscoreindex.php?id=<?php echo($id); ?>"</script><?php
            }elseif($index==""){
                ?><script>alert("請輸入指標");location.href="neweditscoreindex.php?id=<?php echo($id); ?>"</script><?php
            }else{
                query($db,"INSERT INTO `scoreindex`(`project_facingid`,`index`)VALUES(?,?)",[$id,$index]);
                query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$data,"新增評分指標",$time,""]);
                ?><script>alert("新增成功");location.href="scoreindex.php?id=<?php echo($id); ?>"</script><?php
            }
        }

        if(isset($_POST["edit"])){
            $index=$_POST["index"];
            $id=$_SESSION["id"];
            $scoreindexid=$_SESSION["scoreindexid"];
            $row=query($db,"SELECT*FROM `scoreindex` WHERE `index`='$index'AND`project_facingid`='$id'")[0];
            if($row&&$row[0]!=$scoreindexid){
                ?><script>alert("帳號已被註冊");location.href="neweditscoreindex.php?id=<?php echo($id); ?>&edit=<?php echo($scoreindexid);?>"</script><?php
            }elseif($index==""){
                ?><script>alert("請輸入指標");location.href="neweditscoreindex.php?id=<?php echo($id); ?>&edit=<?php echo($scoreindexid);?>"</script><?php
            }else{
                query($db,"UPDATE `scoreindex` SET `index`=? WHERE `id`='$scoreindexid'",[$index]);
                query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$data,"編輯評分指標",$time,""]);
                ?><script>alert("修改成功");location.href="scoreindex.php?id=<?php echo($id); ?>"</script><?php
            }
        }

        if(isset($_GET["del"])){
            $id=$_GET["del"];
            if($row=query($db,"SELECT*FROM `scoreindex` WHERE `id`='$id'")[0]){
                query($db,"DELETE FROM `scoreindex` WHERE `id`='$id'");
                query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$data,"刪除評分指標",$time,""]);
                ?><script>alert("刪除成功!");location.href="scoreindex.php?id=<?php echo($_GET["id"]); ?>"</script><?php
            }else{ ?><script>alert("查無此資料!");location.href="scoreindex.php?id=<?php echo($_GET["id"]); ?>"</script><?php }
        }
    ?>
    </body>
</html>