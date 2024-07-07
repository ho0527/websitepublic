<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>管理者專區</title>
        <link rel="stylesheet" href="/index.css">
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="/chrisplugin/css/chrisplugin.css">
        <script src="https://chrisplugin.pages.dev/js/chrisplugin.js"></script>
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){ header("location:index.php"); }
        ?>
        <div class="navigationbar">
            <div class="navigationbarleft">
                <div class="navigationbartitle">專案討論系統</div>
            </div>
            <div class="navigationbarright">
                <input type="button" class="navigationbarbutton" onclick="location.href='neweditproject.php'" value="新增專案">
                <?php
                    if($_SESSION["data"]==1){ ?><input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="使用者管理"><?php }
                ?>
                <input type="button" class="navigationbarbutton" onclick="location.href='project.php'" value="專案管理">
                <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='teamleader.php'" value="組長功能管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='statistic.php'" value="統計管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
            </div>
        </div>
        <div class="main mainmain center macossectiondivy">
            <table class="sttable textcenter">
                <tr>
                    <td class="maintd">name</td>
                    <td class="maintd">desciption</td>
                    <td class="maintd">function</td>
                </tr>
                <?php
                    $data=$_SESSION["data"];
                    $row=query($db,"SELECT*FROM `project`");
                    for($i=0;$i<count($row);$i=$i+1){
                        $leader=$row[$i][4];
                        $mamber=explode("|&|",$row[$i][5]);
                        $key=false;
                        for($j=0;$j<count($mamber);$j=$j+1){
                            if($mamber[$j]==$data){
                                $key=true;
                                break;
                            }
                        }
                        if($data=="1"||$leader==$data){
                            ?>
                            <tr>
                                <td class="maintd"><?php echo($row[$i][1]); ?></td>
                                <td class="maintd"><?php echo($row[$i][2]); ?></td>
                                <td class="maintd">
                                    <input type="button" class="stbutton outline" onclick="location.href='scoreindex.php?id=<?php echo($row[$i][0]); ?>'" value="評分指標">
                                    <input type="button" class="stbutton outline" onclick="location.href='plan.php?id=<?php echo($row[$i][0]); ?>'" value="執行方案"><br>
                                    <?php
                                    if($row[$i][7]=="true"){
                                        ?><input type="button" class="stbutton outline" onclick="location.href='api.php?key=canpostopinion&id=<?php echo($row[$i][0]); ?>&value=false'" value="停止發表意見"><?php
                                    }else{
                                        ?><input type="button" class="stbutton outline" onclick="location.href='api.php?key=canpostopinion&id=<?php echo($row[$i][0]); ?>&value=true'" value="開始發表意見"><?php
                                    }
                                    ?>
                                    <?php
                                        if($row[$i][8]=="false"){
                                            ?><input type="button" class="stbutton outline" onclick="location.href='api.php?key=planchange&value=true&id=<?php echo($row[$i][0]); ?>'" value="執行方案開始評分"><?php
                                        }elseif($row[$i][8]=="true"){
                                            ?><input type="button" class="stbutton outline end" data-id="<?php echo($row[$i][0]) ?>" value="執行方案結束評分"><?php
                                        }elseif($row[$i][8]=="check"){
                                            ?><input type="button" class="stbutton outline" onclick="location.href='api.php?key=planchange&value=end&id=<?php echo($row[$i][0]); ?>'" value="執行方案開放檢視評分結果"><?php
                                        }else{
                                            ?><input type="button" class="stbutton outline disabled" value="已完成此項目" disabled><?php
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                ?>
            </table>
        </div>
        <div class="lightboxmask" id="lightbox"></div>
        <script src="teamleader.js"></script>
    </body>
</html>