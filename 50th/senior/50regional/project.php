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
                <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='project.php'" value="專案管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='teamleader.php'" value="組長功能管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='statistic.php'" value="統計管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
            </div>
        </div>
        <div class="main mainmain center macossectiondiv">
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
                        $leader=$row[$i][3];
                        $mamber=explode("|&|",$row[$i][4]);
                        $key=false;
                        for($j=0;$j<count($mamber);$j=$j+1){
                            if($mamber[$j]==$data){
                                $key=true;
                                break;
                            }
                        }
                        if($data=="1"||$leader==$data||$key==true){
                            ?>
                            <tr>
                                <td class="maintd"><?php echo($row[$i][1]); ?></td>
                                <td class="maintd"><?php echo($row[$i][2]); ?></td>
                                <td class="maintd">
                                    <input type="button" class="stbutton small light" onclick="location.href='neweditproject.php?edit=<?php echo($row[$i][0]); ?>'" value="修改">
                                    <input type="button" class="stbutton small negative" onclick="location.href='neweditproject.php?del=<?php echo($row[$i][0]); ?>'" value="刪除"><br>
                                    <input type="button" class="stbutton small outline" onclick="location.href='choosefacing.php?id=<?php echo($row[$i][0]); ?>'" value="專案管理">
                                    <?php
                                        if($row[$i][8]=="end"){
                                            ?><input type="button" class="stbutton small outline" onclick="location.href='planmember.php?id=<?php echo($row[$i][0]); ?>&isend=true'" value="執行方案檢視結果"><?php
                                        }else{
                                            ?><input type="button" class="stbutton small outline" onclick="location.href='planmember.php?id=<?php echo($row[$i][0]); ?>&isend=false'" value="執行方案"><?php
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
    </body>
</html>