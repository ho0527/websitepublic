<!DOCTYPE html>
<html class="">
    <head>
        <meta charset="UTF-8">
        <title>Document</title>
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="/chrisplugin/css/chrisplugin.css">
        <script src="error.js"></script>
        <script src="https://chrisplugin.pages.dev/js/chrisplugin.js"></script>
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){ header("location:index.php"); }
        ?>
        <div class="navigationbar">
            <div class="navigationbarleft"><div class="navigationbartitle">網路問卷管理系統</div></div>
            <div class="navigationbarright">
                <input type="button" class="navigationbarbutton" id="changecolor" value="變色:)">
                <input type="button" class="navigationbarbutton" onclick="location.href='index.php'" value="首頁">
                <input type="button" class="navigationbarbutton" id="newform" value="新增問卷">
                <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
            </div>
        </div>
        <div class="macosmaindiv macossectiondivy">
            <table class="sttable textcenter">
                <tr>
                    <td>標題</td>
                    <td>邀請碼</td>
                    <td>填寫份數</td>
                    <td>功能</td>
                </tr>
                <?php
                $data=$_SESSION["data"];
                $row=query($db,"SELECT*FROM `question` WHERE `ps`!='del'");
                $userrow=query($db,"SELECT*FROM `user` WHERE `id`=?",[$data])[0];
                for($i=0;$i<count($row);$i=$i+1){
                    $id=$row[$i][0];
                    $coderow=query($db,"SELECT*FROM `questioncode` WHERE `questionid`='$id'");
                    ?>
                    <tr id="<?php echo($id) ?>">
                        <td><?php echo($row[$i][1]) ?></td>
                        <td>
                            <?php
                                for($j=0;$j<count($coderow);$j=$j+1){
                                    if($coderow[$j][2]==""){ echo($coderow[$j][3]."<br>"); }
                                    else{ echo($coderow[$j][2]." => ".$coderow[$j][3]."<br>"); }
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                            if($row[$i][6]!=""){ echo($row[$i][4]."/".$row[$i][6]); }
                            else{ echo($row[$i][4]); }
                            ?>
                        </td>
                        <td>
                            <?php
                            $value="鎖定";
                            $disabled="";
                            if($row[$i][5]=="true"){
                                $value="解鎖";
                                $disabled="disabled";
                            }
                            if($_SESSION["data"]==1 ){
                                ?>
                                <input type="button" class="stbutton outline workbutton" onclick="location.href='?mod=lock&id=<?php echo($row[$i][0]) ?>'" value="<?php echo($value) ?>"><br>
                                <input type="button" class="stbutton light workbutton <?php echo($disabled) ?>" onclick="location.href='?mod=edit&id=<?php echo($row[$i][0]) ?>'" value="編輯" <?php echo($disabled) ?>><br>
                                <input type="button" class="stbutton error workbutton <?php echo($disabled) ?>" onclick="location.href='?mod=del&id=<?php echo($row[$i][0]) ?>'" value="刪除" <?php echo($disabled) ?>><br>
                                <input type="button" class="stbutton outline workbutton" onclick="location.href='?mod=responelist&id=<?php echo($row[$i][0]) ?>'" value="回應內容"><br>
                                <input type="button" class="stbutton outline workbutton" onclick="location.href='?mod=result&id=<?php echo($row[$i][0]) ?>'" value="統計結果"><br>
                                <input type="button" class="stbutton outline workbutton" onclick="location.href='?mod=output&id=<?php echo($row[$i][0]) ?>'" value="輸出問卷"><br>
                                <input type="button" class="stbutton outline workbutton" onclick="location.href='?mod=copyquestion&id=<?php echo($row[$i][0]) ?>'" value="複製問題"><br>
                                <input type="button" class="stbutton outline workbutton" onclick="location.href='?mod=copyall&id=<?php echo($row[$i][0]) ?>'" value="複製全部"><br>
                                <?php
                            }
                            $checkrow=query($db,"SELECT*FROM `questioncode` WHERE `questionid`='$id'AND(`user`=?OR`user`='')",[$userrow[1]]);
                            if($checkrow||count($coderow)==0){
                                ?><input type="button" class="stbutton outline workbutton" onclick="location.href='?mod=respone&id=<?php echo($row[$i][0]) ?>'" value="填寫問卷"><?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        <div id="lightbox"></div>
        <?php
            if(isset($_GET["submit"])){
                $title=$_GET["title"];
                $count=$_GET["count"];
                $pagelen=$_GET["pagelen"];
                $row=query($db,"SELECT*FROM `question` WHERE `title`=?",[$title])[0];
                if($title==""){
                    ?><script>alert("[WARNING]請輸入問卷標題");location.href="admin.php"</script><?php
                }if($pagelen=="0"){
                    ?><script>alert("[WARNING]分頁數不得為0");location.href="admin.php"</script><?php
                }elseif($row){
                    ?><script>alert("[WARNING]問卷已存在");location.href="admin.php"</script><?php
                }elseif(preg_match("/^[0-9]+$/",$count)&&preg_match("/^-?[0-9]+$/",$pagelen)){
                    query($db,"INSERT INTO `question`(`title`,`questioncount`,`pagelen`,`responcount`,`lock`)VALUES(?,?,'$pagelen','0','false')",[$title,$count]);
                    $_SESSION["title"]=$title;
                    $_SESSION["count"]=$count;
                    $row=query($db,"SELECT*FROM `question` WHERE `title`=?",[$title])[0];
                    $_SESSION["id"]=$row[0];
                    $id=$_SESSION["id"];
                    query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','新增問卷','$time','qid=$id')");
                    ?><script>location.href="form.php"</script><?php
                }else{
                    ?><script>alert("[WARNING]問卷題數和分頁長度請輸入數字");location.href="admin.php"</script><?php
                }
            }
            if(isset($_GET["mod"])){
                $id=$_GET["id"];
                $mod=$_GET["mod"];
                $row=query($db,"SELECT*FROM `question` WHERE `id`='$id'")[0];
                if($mod=="lock"||$mod=="edit"||$mod=="del"){
                    if($row[5]=="false"){
                        if($mod=="lock"){
                            query($db,"UPDATE `question` SET `lock`='true' WHERE `id`='$id'");
                            query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','鎖定問卷','$time','qid=$id')");
                        }elseif($mod=="edit"){
                            $_SESSION["id"]=$row[0];
                            $_SESSION["count"]=$row[2];
                            query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','編輯問卷','$time','qid=$id')");
                            ?><script>location.href="form.php"</script><?php
                        }elseif($mod=="del"){
                            ?><script>
                                if(confirm("Are you sure you want to delete?")){ location.href="api.php?formdel=&id=<?php echo($id) ?>" }
                                else{ location.href="admin.php" }
                            </script><?php
                        }else{
                            ?><script>e4032();location.href="admin.php"</script><?php
                            query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','一個白癡改了getname','$time','')");
                        }
                    }elseif($row[5]=="true"){
                        if($mod=="lock"){
                            query($db,"UPDATE `question` SET `lock`='false' WHERE `id`='$id'");
                            query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','解鎖問卷','$time','qid=$id')");
                        }
                        elseif($mod=="edit"||$mod=="del"){
                            ?><script>e403();location.href="admin.php"</script><?php
                            query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','一個白癡改了原始碼','$time','')");
                        }
                        else{
                            ?><script>e4032();location.href="admin.php"</script><?php
                            query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','一個白癡改了getname','$time','')");
                        }
                    }else{ ?><script>sql001();location.href="admin.php"</script><?php }
                    ?><script>location.href="admin.php"</script><?php
                }elseif($mod=="copyquestion"||$mod=="copyall"){
                    $row=query($db,"SELECT*FROM `question` WHERE `id`='$id'")[0];
                    $title=$row[1];
                    for($i=0;$i<1000000000000000000000000;$i=$i+1){
                        if($mod=="copyquestion"){ $title="copy ".$title; }else{ $title=$title." copy"; }
                        if(!query($db,"SELECT*FROM `question` WHERE `title`='$title'")[0]){ break; }
                    }
                    $questionlog=$row[7];
                    if($mod=="copyquestion"){
                        query($db,"INSERT INTO `question`(`title`,`questioncount`,`pagelen`,`responcount`,`lock`,`maxlen`,`log`,`updatetime`,`ps`)VALUES(?,?,?,?,?,?,?,'$time','')",[$title,$row[2],$row[3],0,"false",$row[6],'']);
                    }else{
                        query($db,"INSERT INTO `question`(`title`,`questioncount`,`pagelen`,`responcount`,`lock`,`maxlen`,`log`,`updatetime`,`ps`)VALUES(?,?,?,?,?,?,?,'$time','')",[$title,$row[2],$row[3],0,"false",$row[6],$questionlog]);
                    }
                    $temp="all";
                    if($mod=="copyquestion"){ $temp="question"; }
                    query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','複製問卷\($temp\)','$time','qid=$id')");
                    ?><script>alert("成功");location.href="admin.php"</script><?php
                }elseif($mod=="responelist"){
                    $id=$_GET["id"];
                    $_SESSION["id"]=$id;
                    query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','查看問卷填寫結果','$time','qid=$id')");
                    ?><script>location.href="responselist.html"</script><?php
                }elseif($mod=="result"){
                    // $id=$_GET["id"];
                    // $_SESSION["id"]=$id;
                    // query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','填寫問卷','$time','qid=$id')");
                    ?><script>//location.href="user.php"</script><?php
                }elseif($mod=="respone"){
                    $id=$_GET["id"];
                    $_SESSION["id"]=$id;
                    query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$data','填寫問卷','$time','qid=$id')");
                    ?><script>location.href="user.php"</script><?php
                }else{
                    ?><script>e404();location.href="admin.php"</script><?php
                }
            }
        ?>
        <script src="initialization.js"></script>
        <script src="admin.js"></script>
    </body>
</html>