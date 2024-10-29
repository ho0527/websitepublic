<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>新增修改專案</title>
        <link rel="stylesheet" href="/website/index.css">
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="plugin/css/macossection.css">
        <link rel="stylesheet" href="plugin/css/sort.css">
        <link rel="stylesheet" href="plugin/css/chrisplugin.css">
        <script src="plugin/js/macossection.js"></script>
        <script src="plugin/js/sort.js"></script>
        <script src="plugin/js/chrisplugin.js"></script>
    </head>
    <body>
        <script> let key=""; </script>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){ header("location:index.php"); }
            if(isset($_GET["edit"])){
                $id=$_GET["edit"];
                $facingrow=query($db,"SELECT*FROM `facing` WHERE `projectid`='$id'");
                if($row=query($db,"SELECT*FROM `project` WHERE `id`='$id'")[0]){
                    $_SESSION["id"]=$id;
                    ?>
                    <script>
                        key="edit"
                        let row=<?php echo(json_encode($row)) ?>;
                        let facingrow=<?php echo(json_encode($facingrow)) ?>;
                    </script>
                    <div class="navigationbar">
                        <div class="navigationbarleft">
                            <div class="navigationbartitle">專案討論系統</div>
                        </div>
                        <div class="navigationbarright">
                            <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='neweditproject.php'" value="修改">
                            <?php
                                if($_SESSION["data"]==1){ ?><input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="使用者管理"><?php }
                            ?>
                            <input type="button" class="navigationbarbutton" onclick="location.href='project.php'" value="專案管理">
                            <input type="button" class="navigationbarbutton" onclick="location.href='teamleader.php'" value="組長功能管理">
                            <input type="button" class="navigationbarbutton" onclick="location.href='statistic.php'" value="統計管理">
                            <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
                        </div>
                    </div>
                    <div class="main center noborder textcenter">
                        <div class="projectgrid">
                            <div class="project">
                                <input type="text" class="middleinput stinput" name="name" id="name" placeholder="專案名稱">
                                <input type="text" class="middleinput stinput" name="desciption" id="desciption" placeholder="專案說明">
                                <input type="button" class="stbutton light" id="submit" value="送出">
                            </div>
                            <div class="projectmember grid">
                                <div class="leader sort macossectiondivy" id="leader">
                                組長
                                <hr>
                                </div>
                                <div class="member sort macossectiondivy" id="member">
                                    組員
                                    <hr>
                                </div>
                                <div class="userdiv sort macossectiondivy" id="user">
                                    使用者列表
                                    <hr>
                                    <?php
                                    $userrow=query($db,"SELECT*FROM `user`");
                                    for($i=0;$i<count($userrow);$i=$i+1){
                                        ?><div class="user" data-id="<?php echo($userrow[$i][0]); ?>"><?php echo($userrow[$i][1]); ?></div><?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="projectfacing macossectiondivy">
                                <input type="button" class="stbutton secondary" id="newfacing" value="新增面向">
                                <div class="facingmaindiv macossectiondivy" id="projectfacing"></div>
                            </div>
                        </div>
                    </div>
                    <?php
                }else{ ?><script>alert("查無此專案");location.href="project.php"</script><?php }
            }else{
                ?>
                <script> key="new"; </script>
                <div class="navigationbar">
                    <div class="navigationbarleft">
                        <div class="navigationbartitle">專案討論系統</div>
                    </div>
                    <div class="navigationbarright">
                        <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='neweditproject.php'" value="新增">
                            <?php
                                if($_SESSION["data"]==1){ ?><input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="使用者管理"><?php }
                            ?>
                        <input type="button" class="navigationbarbutton" onclick="location.href='project.php'" value="專案管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='teamleader.php'" value="組長功能管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='statistic.php'" value="統計管理">
                        <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
                    </div>
                </div>
                <div class="main center noborder textcenter">
                    <div class="projectgrid">
                        <div class="project">
                            <input type="text" class="middleinput stinput" name="name" id="name" placeholder="專案名稱">
                            <input type="text" class="middleinput stinput" name="desciption" id="desciption" placeholder="專案說明">
                            <input type="button" class="stbutton light" id="submit" value="送出">
                        </div>
                        <div class="projectmember grid">
                            <div class="leader sort macossectiondivy" id="leader">
                            組長
                            <hr>
                            </div>
                            <div class="member sort macossectiondivy" id="member">
                                組員
                                <hr>
                            </div>
                            <div class="userdiv sort macossectiondivy" id="user">
                                使用者列表
                                <hr>
                                <?php
                                $userrow=query($db,"SELECT*FROM `user`");
                                for($i=0;$i<count($userrow);$i=$i+1){
                                    ?><div class="user" data-id="<?php echo($userrow[$i][0]); ?>"><?php echo($userrow[$i][1]); ?></div><?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="projectfacing">
                            <input type="button" class="stbutton secondary" id="newfacing" value="新增面向">
                            <div class="facingmaindiv macossectiondivy" id="projectfacing">
                                <div class="facingdiv">
                                    <div class="facing grid">
                                        <input type="text" class="input2 facingname" placeholder="面向名稱">
                                        <input type="text" class="input2 facingdesciption" placeholder="面向說明">
                                        <input type="button" class="noborderbutton facingdelect" value="X">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            if(isset($_GET["del"])){
                $id=$_GET["del"];
                $_SESSION["id"]=$id;
                if($row=query($db,"SELECT*FROM `project` WHERE `id`=?",[$id])[0]){
                    ?>
                    <script>
                        if(confirm("Are you sure you want to delete?")){
                            location.href="api.php?projectdel="
                        }else{
                            location.href="project.php"
                        }
                    </script>
                    <?php
                }else{ ?><script>alert("面相不存在或已被刪除!");location.href="project.php"</script><?php }
            }
        ?>
        <script src="neweditproject.js"></script>
    </body>
</html>