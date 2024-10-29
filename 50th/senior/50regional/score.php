<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>管理者專區</title>
        <link rel="stylesheet" href="/website/index.css">
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="plugin/css/macossection.css">
        <script src="plugin/js/macossection.js"></script>
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){ header("location:index.php"); }
            if(!isset($_GET["id"])){ header("location:project.php"); }
            if(!isset($_GET["key"])){ header("location:project.php"); }
            if($_GET["key"]=="opinion"){
                $_SESSION["id"]=$_GET["id"];
                $_SESSION["opinionid"]=$_GET["opinionid"];
            }else{
                $_SESSION["id"]=$_GET["id"];
                $_SESSION["projectid"]=$_GET["projectid"];
            }
            // $planrow=query($db,"SELECT*FROM `plan` WHERE `id`=?",[$_SESSION["id"]])
        ?>
        <div class="navigationbar">
            <div class="navigationbarleft">
                <div class="navigationbartitle">專案討論系統</div>
            </div>
            <div class="navigationbarright">
                <input type="button" class="navigationbarbutton" onclick="location.href='neweditproject.php?id=<?php echo($_GET['id']); ?>'" value="發表意見">
                <?php
                    if($_SESSION["data"]==1){ ?><input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="使用者管理"><?php }
                ?>
                <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='project.php'" value="專案管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='teamleader.php'" value="組長功能管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='statistic.php'" value="統計管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
            </div>
        </div>
        <div class="main center macossectiondiv">
            <form method="POST">
                <div class="inputmargin">
                    <div class="sttext">分數</div>
                    <div class="stinput underline">
                        <input type="number" name="score" value="3" min="1" max="5" step="1">
                    </div>
                </div>
                <input type="button" class="stbutton outline" onclick="location.href='viewplan.php?id=<?php echo($_GET['id']); ?>'" value="查看意見">
                <input type="submit" class="stbutton outline" name="submit" value="送出">
            </form>
        </div>
        <?php
            if(isset($_POST["submit"])){
                if($_GET["key"]=="opinion"){
                    $userdata=$_SESSION["data"];
                    $score=$_POST["score"];
                    $opinionid=$_SESSION["opinionid"];
                    $id=$_SESSION["id"];
                    if(1<=$score&&$score<=5){
                        query($db,"INSERT INTO `score`(`userid`,`opinionid`,`score`)VALUES(?,?,?)",[$userdata,$opinionid,$score]);
                        query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$userdata,"評分",$time,"opitionid=".$opinionid]);
                        ?><script>alert("評分成功");location.href="opinion.php?id=<?php echo($id); ?>"</script><?php
                    }else{
                        ?><script>alert("分數不正確");location.href="newscore.php?key=opinion&id=<?php echo($id); ?>&opinionid=<?php echo($opinionid); ?>"</script><?php
                    }
                }else{
                    $userdata=$_SESSION["data"];
                    $score=$_POST["score"];
                    $projectid=$_SESSION["projectid"];
                    $id=$_SESSION["id"]; // planid
                    if(1<=$score&&$score<=5){
                        query($db,"INSERT INTO `planscore`(`userid`,`planid`,`score`)VALUES(?,?,?)",[$userdata,$id,$score]);
                        query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$userdata,"評分",$time,"planid=".$id]);
                        ?><script>alert("評分成功");location.href="planmember.php?id=<?php echo($projectid); ?>"</script><?php
                    }else{
                        ?><script>alert("分數不正確");location.href="newscore.php?key=plan&id=<?php echo($id); ?>&projectid=<?php echo($projectid); ?>"</script><?php
                    }
                }
            }
        ?>
    </body>
</html>