<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>編輯問卷</title>
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="/website/plugin/css/chrisplugin.css">
        <script src="error.js"></script>
        <script src="/website/plugin/js/chrisplugin.js"></script>
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){ header("location:index.php"); }
            if(!isset($_SESSION["id"])){ header("location:form.php"); }
            $id=$_SESSION["id"];
            $row=query($db,"SELECT*FROM `question` WHERE `id`='$id'")[0];
        ?>
        <div class="navigationbar">
            <div class="ˊㄊ">
                <div class="stinput basic forminput" style="width:170px">
                    標題: <input type="text" class="textcenter textunderline" id="title" style="width:120px">
                </div>
                <div class="stinput basic forminput" style="width:100px">
                    總數: <input type="text" class="textcenter textunderline" id="count" style="width:50px" readonly>
                </div>
                <div class="stinput basic forminput" style="width:130px">
                    分頁題數: <input type="text" class="textcenter textunderline" id="pagelen" style="width:50px">
                </div>
                <div class="stinput basic forminput" style="width:130px">
                    最大總數: <input type="text" class="textcenter textunderline" id="maxcount" style="width:50px">
                </div>
            </div>
            <div class="navigationbarright">
                <input type="button" class="navigationbarbutton" onclick="location.href='questioncode.php'" value="問卷邀請碼">
                <input type="button" class="navigationbarbutton" onclick="newquestion()" value="新增題目">
                <input type="button" class="navigationbarbutton" onclick="save()" value="儲存問卷">
                <input type="button" class="navigationbarbutton" onclick="location.href='api.php?cancel='" value="返回">
                <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
            </div>
        </div>

        <div class="macosmaindiv macossectiondiv formmaindiv" id="maindiv"></div>

        <script>
            let row=<?php echo(json_encode($row)) ?>;
            let questionrow=<?php echo(json_encode($row[7])) ?>;
        </script>
        <script src="form.js"></script>
    </body>
</html>