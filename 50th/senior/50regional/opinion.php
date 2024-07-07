<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>管理者專區</title>
        <link rel="stylesheet" href="/website/index.css">
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="/website/plugin/css/chrisplugin.css">
        <script src="/website/plugin/js/chrisplugin.js"></script>
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){ header("location:index.php"); }
            if(!isset($_GET["id"])){ header("location:project.php"); }
            $id=$_GET["id"];
            $projectid=explode("_",$id)[0];
        ?>
        <div class="navigationbar">
            <div class="navigationbarleft">
                <div class="navigationbartitle">專案討論系統</div>
            </div>
            <div class="navigationbarright">
                <?php
                    $row=query($db,"SELECT*FROM `project` WHERE `id`='$projectid'")[0];
                    if($row[7]=="true"){ ?><input type="button" class="navigationbarbutton" onclick="location.href='newopinion.php?id=<?php echo($_GET['id']); ?>'" value="發表意見"><?php }
                    if($_SESSION["data"]==1){ ?><input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="使用者管理"><?php }
                ?>
                <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='project.php'" value="專案管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='teamleader.php'" value="組長功能管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='statistic.php'" value="統計管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
            </div>
        </div>
        <div class="main mainmain center opinionmain macossectiondivy">
            <?php
                $userdata=$_SESSION["data"];
                $row=query($db,"SELECT*FROM `opinion` WHERE `project_facingid`='$id'");
                for($i=0;$i<count($row);$i=$i+1){
                    $userrow=query($db,"SELECT*FROM `user` WHERE `id`=?",[$row[$i][1]])[0];
                    $opinionid=$row[$i][0];
                    $scorerow=query($db,"SELECT*FROM `score` WHERE `opinionid`='$opinionid'");
                    $totalscore=0;
                    $count=count($scorerow);
                    $usercheck=true;
                    for($j=0;$j<$count;$j=$j+1){
                        $totalscore=$totalscore+$scorerow[$j][3];
                        $scoreuserid=$scorerow[$j][1];
                        $scoreuseerrow=query($db,"SELECT*FROM `user` WHERE `id`='$scoreuserid'")[0];
                        if($scoreuseerrow[0]==$userdata){ $usercheck=false; }
                    }
                    if($count>0){ $averagescore=$totalscore/$count; }
                    else{ $averagescore=0; }
                    $extendidlist=explode("|&|",$row[$i][3]);
                    $extend=[];
                    if($extendidlist[0]!=""){
                        for($j=0;$j<count($extendidlist);$j=$j+1){
                            $extendid=$extendidlist[$j];
                            $opinionrow=query($db,"SELECT*FROM `opinion` WHERE `id`='$extendid'")[0];
                            $extend[]="<a href='#' onclick=\"smoothscroll('opinion".$extendid."')\">".$opinionrow[4]."</a>";
                        }
                        if(count($extend)>0){ $extend=implode(",",$extend); }
                    }else{ $extend="無引用"; }
                    ?>
                    <div class="opinion" id="opinion<?php echo($row[$i][0]); ?>">
                        <div class="no">編號: <?php echo($row[$i][9]); ?></div>
                        <div class="extend">引用: <?php echo($extend); ?></div>
                        <div class="postuser">發表者: <?php echo($userrow[1]); ?></div>
                        <div class="title">標題: <?php echo($row[$i][4]); ?></div>
                        <div class="description">說明: <?php echo($row[$i][5]); ?></div>
                        <div class="time">被評價的平均分數: <?php echo($averagescore); ?></div>
                        <div class="time">評價人數: <?php echo($count); ?></div>
                        <?php
                            if($usercheck){
                                ?><input type="button" class="stbutton light" onclick="location.href='score.php?key=opinion&id=<?php echo($_GET['id']); ?>&opinionid=<?php echo($row[$i][0]); ?>'" value="評價"><?php
                            }else{
                                ?><input type="button" class="stbutton light disabled" value="已完成評價" disabled><?php
                            }

                            if($row[$i][7]=="audio"){
                                ?><div class="mediadiv"><audio class="media mediaaudiovideo" controls><source src="<?php echo($row[$i][6]); ?>" type="audio/mpeg"></audio></div><?php
                            }elseif($row[$i][7]=="video"){
                                ?><div class="mediadiv"><video class="media mediaaudiovideo" controls><source src="<?php echo($row[$i][6]); ?>" type="video/mp4"></video></div><?php
                            }elseif($row[$i][7]=="image"){
                                ?><div class="mediadiv"><img src="<?php echo($row[$i][6]); ?>" class="media"></div><?php
                            }else{ }
                        ?>
                        <div class="time">發佈時間: <?php echo($row[$i][8]); ?></div>
                        <?php
                            if($i<count($row)-1){
                                ?><hr><?php
                            }
                        ?>
                    </div>
                    <?php
                }
            ?>
        </div>
        <script src="opinion.js"></script>
    </body>
</html>