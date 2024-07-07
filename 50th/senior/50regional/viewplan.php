<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>執行方案列表</title>
        <link rel="stylesheet" href="/index.css">
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="plugin/css/chrisplugin.css">
        <script src="plugin/js/chrisplugin.js"></script>
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){ header("location:index.php"); }
            if(!isset($_GET["id"])){ header("location:project.php"); }
            $planid=$_GET["id"];
            $row=query($db,"SELECT*FROM `plan` WHERE `id`='$planid'")[0];
        ?>
        <div class="navigationbar">
            <div class="navigationbarleft">
                <div class="navigationbartitle">專案討論系統</div>
            </div>
            <div class="navigationbarright">
                <?php
                    $projectid=$row[1];
                    $projectrow=query($db,"SELECT*FROM `project` WHERE `id`='$projectid'")[0];
                    if($projectrow[7]=="true"){ ?><input type="button" class="navigationbarbutton" onclick="location.href='newopinion.php?id=<?php echo($projectid); ?>'" value="發表意見"><?php }
                    if($_SESSION["data"]==1){ ?><input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="使用者管理"><?php }
                ?>
                <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='project.php'" value="專案管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='teamleader.php'" value="組長功能管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='statistic.php'" value="統計管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
            </div>
        </div>
        <div class="main mainmain center viewmain macossectiondiv">
            <table class="sttable textcenter">
                <tr>
                    <td class="maintd">no.</td>
                    <td class="maintd">facingname</td>
                    <td class="maintd">opinionname</td>
                    <td class="maintd">function</td>
                </tr>
                <?php
                    $userdata=$_SESSION["data"];
                    $facingopinionidlist=explode("|&|",$row[4]);
                    if($facingopinionidlist[0]!=""){
                        for($i=0;$i<count($facingopinionidlist);$i=$i+1){
                            $facingid=explode("_",$facingopinionidlist[$i])[0];
                            $opinionid=explode("_",$facingopinionidlist[$i])[1];
                            $facingrow=query($db,"SELECT*FROM `facing` WHERE `id`='$facingid'")[0];
                            $opinionrow=query($db,"SELECT*FROM `opinion` WHERE `id`='$opinionid'")[0];
                            ?>
                            <tr>
                                <td class="maintd">意見<?php echo($i+1); ?>:</td>
                                <td class="maintd"><?php echo($facingrow[2]); ?></td>
                                <td class="maintd"><?php echo($opinionrow[4]); ?></td>
                                <td class="maintd">
                                    <input type="button" class="stbutton outline viewbutton" data-id="<?php echo($opinionid); ?>" value="查看意見">
                                </td>
                            </tr>
                            <?php
                        }
                    }else{
                        ?>
                        <tr>
                            <td class="maintd sttext negative massive bold" colspan="4">暫無資料</td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
        </div>
        <div class="lightboxmask" id="lightbox"></div>
        <script src="viewplan.js"></script>
    </body>
</html>