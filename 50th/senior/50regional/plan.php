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
                <input type="button" class="navigationbarbutton" onclick="location.href='project.php'" value="專案管理">
                <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='teamleader.php'" value="組長功能管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='statistic.php'" value="統計管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
            </div>
        </div>
        <div class="main mainmain center macossectiondiv">
            <table class="sttable textcenter">
                <form>
                    <tr>
                        <td class="maintd">title</td>
                        <td class="maintd">desciption</td>
                        <td class="maintd">function</td>
                    </tr>
                    <?php
                        $id=$_GET["id"];
                        $row=query($db,"SELECT*FROM `plan` WHERE `projectid`='$id'");
                        for($i=0;$i<count($row);$i=$i+1){
                            ?>
                                <tr>
                                    <td class="maintd"><?php echo($row[$i][2]); ?></td>
                                    <td class="maintd"><?php echo($row[$i][3]); ?></td>
                                    <td class="maintd">
                                        <input type="button" class="stbutton light" onclick="location.href='neweditplan.php?id=<?php echo($id); ?>&edit=<?php echo($row[$i][0]); ?>'" value="修改">
                                        <input type="button" class="stbutton negative" onclick="location.href='neweditplan.php?id=<?php echo($id); ?>&del=<?php echo($row[$i][0]); ?>'" value="刪除"><br>
                                    </td>
                                </tr>
                            <?php
                        }
                    ?>
                </form>
            </table>
        </div>
    </body>
</html>