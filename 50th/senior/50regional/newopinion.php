<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>新增意見</title>
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
            $_SESSION["id"]=$_GET["id"];
        ?>
        <div class="navigationbar">
            <div class="navigationbarleft">
                <div class="navigationbartitle">專案討論系統</div>
            </div>
            <div class="navigationbarright">
                <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='newopinion.php?id=<?php echo($_GET['id']); ?>'" value="發表意見">
                <?php
                    if($_SESSION["data"]==1){ ?><input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="使用者管理"><?php }
                ?>
                <input type="button" class="navigationbarbutton" onclick="location.href='project.php'" value="專案管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='teamleader.php'" value="組長功能管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='statistic.php'" value="統計管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
            </div>
        </div>
        <div class="main center macossectiondiv">
            <form method="POST" enctype="multipart/form-data">
                <div class="inputmargin">
                    <div class="sttext">標題</div>
                    <div class="stinput underline">
                        <input type="text" class="input" id="title">
                    </div>
                </div>
                <div class="stinput">
                    <textarea class="resizeable" id="description" placeholder="說明"></textarea>
                </div>
                <div class="opinionlistdivdiv textcenter">
                    <div class="opinionlistdiv">
                        意見列表
                        <hr>
                        <div class="opinionlist sort macossectiondivy">
                            <?php
                                $row=query($db,"SELECT*FROM `opinion` WHERE `project_facingid`=?",[$_GET["id"]]);
                                for($i=0;$i<count($row);$i=$i+1){
                                    ?><div class="opiniondiv" data-id="<?php echo($row[$i][0]); ?>"><?php echo($row[$i][4]); ?></div><?php
                                }
                            ?>
                        </div>
                    </div>
                    <div class="opinionlistdiv">
                        延伸意見
                        <hr>
                        <div class="opinionlist sort macossectiondivy" id="extend">
                        </div>
                    </div>
                </div><br>
                <div class="textcenter">
                    <input type="file" class="file" id="file">
                    <input type="reset" class="stbutton outline" value="清除">
                    <input type="button" class="stbutton outline" id="submit" value="送出">
                </div>
            </form>
        </div>
        <script src="newopinion.js"></script>
    </body>
</html>