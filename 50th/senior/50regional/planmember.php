<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
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
        ?>
        <div class="navigationbar">
            <div class="navigationbarleft">
                <div class="navigationbartitle">專案討論系統</div>
            </div>
            <div class="navigationbarright">
                <input type="button" class="navigationbarbutton" onclick="location.href='neweditplan.php?id=<?php echo($_GET['id']); ?>'" value="新增執行方案">
                <?php
                    if($_SESSION["data"]==1){ ?><input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="使用者管理"><?php }
                ?>
                <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='project.php'" value="專案管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='teamleader.php'" value="組長功能管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='statistic.php'" value="統計管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
            </div>
        </div>
        <div class="main mainmain center macossectiondiv">
            <table class="sttable textcenter">
                <form>
                    <?php
                        $id=$_GET["id"];
                        $row=query($db,"SELECT*FROM `plan` WHERE `projectid`='$id'");
                        $projectrow=query($db,"SELECT*FROM `project` WHERE `id`='$id'")[0];
                        if($_GET["isend"]=="false"){
                            ?>
                            <tr>
                                <td class="maintd">title</td>
                                <td class="maintd">desciption</td>
                                <td class="maintd">function</td>
                            </tr>
                            <?php
                            $leader=$projectrow[3];
                            for($i=0;$i<count($row);$i=$i+1){
                                $planid=$row[$i][0];
                                $scorerow=query($db,"SELECT*FROM `planscore` WHERE `planid`='$planid'");
                                $uservotecheck=true;
                                for($j=0;$j<count($scorerow);$j=$j+1){
                                    $scoreuserid=$scorerow[$j][1];
                                    $scoreuseerrow=query($db,"SELECT*FROM `user` WHERE `id`='$scoreuserid'")[0];
                                    if($scoreuseerrow[0]==$_SESSION["data"]){ $uservotecheck=false; }
                                }
                                ?>
                                <tr>
                                    <td class="maintd"><?php echo($row[$i][2]); ?></td>
                                    <td class="maintd"><?php echo($row[$i][3]); ?></td>
                                    <td class="maintd">
                                        <input type="button" class="stbutton outline" onclick="location.href='viewplan.php?id=<?php echo($row[$i][0]); ?>'" value="查看">
                                        <?php
                                        if($leader!=$_SESSION["data"]||$_SESSION["data"]=="1"){
                                            if($uservotecheck){
                                                if($projectrow[8]=="true"){
                                                    ?><input type="button" class="stbutton outline" onclick="location.href='score.php?key=plan&id=<?php echo($row[$i][0]); ?>&projectid=<?php echo($id); ?>'" value="評分"><div class="warning">尚未填寫此項目!</div><?php
                                                }elseif($projectrow[8]=="false"){
                                                    ?><input type="button" class="stbutton outline disabled" value="評分尚未開始" disabled><?php
                                                }else{
                                                    ?><input type="button" class="stbutton outline disabled" value="已結束評分" disabled><?php
                                                }
                                            }else{
                                                if($projectrow[8]=="end"){
                                                    ?><input type="button" class="stbutton outline" value="檢視結果"><?php
                                                }else{
                                                    ?><input type="button" class="stbutton outline disabled" value="已完成評分" disabled><?php
                                                }
                                            }
                                        }else{
                                            ?><input type="button" class="stbutton outline disabled" value="組長無法評分" disabled><?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }else{
                            ?>
                            <tr>
                                <td class="maintd">no</td>
                                <td class="maintd">title</td>
                                <td class="maintd">desciption</td>
                                <td class="maintd">function</td>
                                <td class="maintd">平均分數</td>
                            </tr>
                            <?php
                            $data=[];
                            for($i=0;$i<count($row);$i=$i+1){
                                $id=$row[$i][0];
                                $rowplanscore=query($db,"SELECT*FROM `planscore` WHERE `planid`='$id'");
                                $score=0;
                                for($j=0;$j<count($rowplanscore);$j=$j+1){
                                    $score=$score+(int)$rowplanscore[$j][3];
                                }
                                $data[]=[$id,$row[$i][2],$row[$i][3],$score/count($rowplanscore)];
                            }

                            usort($data,function($a,$b){
                                if($a[3]<$b[3]){
                                    return 1;
                                }else{
                                    return 0;
                                }
                            });

                            for($i=0;$i<count($data);$i=$i+1){
                                ?>
                                <tr>
                                    <td class="maintd"><?php echo($i+1); ?></td>
                                    <td class="maintd"><?php echo($data[$i][1]); ?></td>
                                    <td class="maintd"><?php echo($data[$i][2]); ?></td>
                                    <td class="maintd"><input type="button" class="stbutton outline" onclick="location.href='viewplan.php?id=<?php echo($row[$i][0]); ?>'" value="查看"></td>
                                    <td class="maintd"><?php echo($data[$i][3]); ?></td>
                                </tr>
                                <?php
                            }
                        }
                    ?>
                </form>
            </table>
        </div>
    </body>
</html>