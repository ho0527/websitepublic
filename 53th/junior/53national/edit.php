<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>南港展覽館接駁專車</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <div class="navigationbar">
            <div class="navigationbarleft">
                <img src="logo.png" class="logo">
                <div class="maintitle">南港展覽館接駁專車系統</div>
            </div>
            <div class="navigationbarright">
                <input type="button" class="navigationbarbutton" onclick="location.href='index.php'" value="首頁">
                <input type="button" class="navigationbarbutton selectbutton" onclick="location.href='login.php'" value="系統管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='link.php?logout='" value="登出">
            </div>
        </div>
        <?php
            include("link.php");
            if(isset($_SESSION["data"])){
                if(isset($_GET["key"])){
                    if($_GET["key"]=="newbus"){
                        ?>
                        <div class="main" id="editchatdiv">
                            <form method="POST">
                                <div class="posttitle">new 車車</div>
                                車&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;牌: <input type="text" class="input2" name="licenseplate"><br><br>
                                已行駛時間(min): <input type="text" class="input2" name="traveltime"><br><br>
                                <input type="button" class="button" onclick="location.href='bus.php'" value="返回">
                                <input type="submit" class="button" name="newcarsubmit" value="送出">
                            </form>
                        </div>
                        <?php
                    }elseif($_GET["key"]=="editbus"){
                        $id=$_GET["id"];
                        if($row=query($db,"SELECT*FROM `bus` WHERE `id`='$id'")){
                            $_SESSION["id"]=$id;
                            ?>
                            <div class="main" id="editchatdiv">
                                <form method="POST">
                                    <div class="posttitle">edit 車車</div>
                                    已行駛時間(min): <input type="text" class="input2" name="traveltime" value="<?php echo($row[0][2]) ?>"><br><br>
                                    <input type="button" class="button" onclick="location.href='bus.php'" value="返回">
                                    <input type="submit" class="button" name="editcarsubmit" value="送出">
                                </form>
                            </div>
                            <?php
                        }else{
                            ?><script>alert("編輯失敗(id不存在)!");location.href="bus.php"</script><?php
                        }
                    }elseif($_GET["key"]=="delbus"){
                        $id=$_GET["id"];
                        if(query($db,"SELECT*FROM `bus` WHERE `id`='$id'")){
                            query($db,"DELETE FROM `bus` WHERE `id`='$id'");
                            ?><script>alert("刪除成功!");location.href="bus.php"</script><?php
                        }else{
                            ?><script>alert("刪除失敗(id不存在)!");location.href="bus.php"</script><?php
                        }
                    }elseif($_GET["key"]=="newsite"){
                        ?>
                        <div class="main" id="editchatdiv">
                            <form method="POST">
                                <div class="posttitle">new 點點</div>
                                站&nbsp;&nbsp;&nbsp;點&nbsp;&nbsp;&nbsp;名&nbsp;&nbsp;&nbsp;稱: <input type="text" class="input2" name="name"><br><br>
                                行駛時間(min): <input type="text" class="input2" name="traveltime"><br><br>
                                停留時間(min): <input type="text" class="input2" name="stoptime"><br><br>
                                <input type="button" class="button" onclick="location.href='site.php'" value="返回">
                                <input type="submit" class="button" name="newsitesubmit" value="送出">
                            </form>
                        </div>
                        <?php
                    }elseif($_GET["key"]=="editsite"){
                        $id=$_GET["id"];
                        if($row=query($db,"SELECT*FROM `site` WHERE `id`='$id'")){
                            $_SESSION["id"]=$id;
                            ?>
                            <div class="main" id="editchatdiv">
                                <form method="POST">
                                    <div class="posttitle">edit 點點</div>
                                    行駛時間(min): <input type="text" class="input2" name="traveltime" value="<?php echo($row[0][2]) ?>"><br><br>
                                    停留時間(min): <input type="text" class="input2" name="stoptime" value="<?php echo($row[0][3]) ?>"><br><br>
                                <input type="button" class="button" onclick="location.href='site.php'" value="返回">
                                    <input type="submit" class="button" name="editsitesubmit" value="送出">
                                </form>
                            </div>
                            <?php
                        }else{
                            ?><script>alert("編輯失敗(id不存在)!");location.href="site.php"</script><?php
                        }
                    }elseif($_GET["key"]=="delsite"){
                        $id=$_GET["id"];
                        if(query($db,"SELECT*FROM `site` WHERE `id`='$id'")){
                            query($db,"DELETE FROM `site` WHERE `id`='$id'");
                            ?><script>alert("刪除成功!");location.href="site.php"</script><?php
                        }else{
                            ?><script>alert("刪除失敗(id不存在)!");location.href="site.php"</script><?php
                        }
                    }else{
                        ?><script>alert("key not exits");location.href="login.php"</script><?php
                    }
                }else{
                    header("location:login.php");
                }
                if(isset($_POST["newcarsubmit"])){
                    $licenseplate=$_POST["licenseplate"];
                    $traveltime=$_POST["traveltime"];
                    if(preg_match("/^[0-9]+(\.[0-9]+)?$/",$traveltime)&&$traveltime>=0){
                        query($db,"INSERT INTO `bus`(`licenseplate`,`traveltime`)VALUES(?,?)",[$licenseplate,$traveltime]);
                        ?><script>alert("新增成功");location.href="bus.php"</script><?php
                    }else{
                        ?><script>alert("已行駛時間只能是數字或浮點數且大於0");location.href="edit.php"</script><?php
                    }
                }
                if(isset($_POST["editcarsubmit"])){
                    $id=$_SESSION["id"];
                    $traveltime=$_POST["traveltime"];
                    if(preg_match("/^[0-9]+(\.[0-9]+)?$/",$traveltime)&&$traveltime>=0){
                        query($db,"UPDATE `bus` SET `traveltime`=? WHERE `id`='$id'",[$traveltime]);
                        unset($_SESSION["id"]);
                        ?><script>alert("修改成功");location.href="bus.php"</script><?php
                    }else{
                        ?><script>alert("已行駛時間只能是數字或浮點數且大於0");location.href="edit.php"</script><?php
                    }
                }
                if(isset($_POST["newsitesubmit"])){
                    $name=$_POST["name"];
                    $traveltime=$_POST["traveltime"];
                    $stoptime=$_POST["stoptime"];
                    if(preg_match("/^[0-9]+(\.[0-9]+)?$/",$traveltime)&&$traveltime>=0){
                        if(preg_match("/^[0-9]+(\.[0-9]+)?$/",$stoptime)&&$stoptime>=0){
                            query($db,"INSERT INTO `site`(`name`,`traveltime`,`stoptime`)VALUES(?,?,?)",[$name,$traveltime,$stoptime]);
                            ?><script>alert("新增成功");location.href="site.php"</script><?php
                        }else{
                            ?><script>alert("停留時間只能是數字或浮點數且大於0");location.href="edit.php"</script><?php
                        }
                    }else{
                        ?><script>alert("行駛時間只能是數字或浮點數且大於0");location.href="edit.php"</script><?php
                    }
                }
                if(isset($_POST["editsitesubmit"])){
                    $id=$_SESSION["id"];
                    $traveltime=$_POST["traveltime"];
                    $stoptime=$_POST["stoptime"];
                    if(preg_match("/^[0-9]+(\.[0-9]+)?$/",$traveltime)&&$traveltime>=0){
                        if(preg_match("/^[0-9]+(\.[0-9]+)?$/",$stoptime)&&$stoptime>=0){
                            query($db,"UPDATE `site` SET `traveltime`=?,`stoptime`=? WHERE `id`='$id'",[$traveltime,$stoptime]);
                            unset($_SESSION["id"]);
                            ?><script>alert("修改成功");location.href="site.php"</script><?php
                        }else{
                            ?><script>alert("停留時間只能是數字或浮點數且大於0");location.href="edit.php"</script><?php
                        }
                    }else{
                        ?><script>alert("行駛時間只能是數字或浮點數且大於0");location.href="edit.php"</script><?php
                    }
                }
            }else{ header("location:login.php"); }
        ?>
    </body>
</html>