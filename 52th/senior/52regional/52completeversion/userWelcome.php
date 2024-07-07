<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>一般會員專區</title>
        <link href="index.css" rel="stylesheet">
    </head>
    <body>
        <?php
            include("link.php");
            include("userdef.php");
            unset($_SESSION["todoval"]);
            $start=$_SESSION["starttime"];
        ?>
        <table class="main-table">
            <tr>
                <td class="date">
                    <form>
                        <?php
                            $date=$_SESSION["date"];
                            echo("目前日期: ".$date);
                        ?><br>
                            <input type="date" value="<?= $date ?>" name="date">
                            <button type="submit" name="enter" id="date-button">送出</button>
                    </form>
                </td>
                <td class="title">一般會員專區</td>
                <td class="all" rowspan="2">
                    <table>
                        <form>
                            <tr>
                                <td class="usertable">編號<input type="submit" name="num-up-down" id="num-up-down" value="升冪"></td>
                                <td class="usertable">標題<input type="submit" name="title-up-down" id="title-up-down" value="升冪"></td>
                                <td class="usertable">日期<input type="submit" name="date-up-down" id="date-up-down" value="升冪"></td>
                                <td class="usertable">時間<input type="submit" name="time-up-down" id="time-up-down" value="升冪"></td>
                                <td class="usertable">處理情形<input type="submit" name="deal-up-down" id="deal-up-down" value="升冪"></td>
                                <td class="usertable">優先順序<input type="submit" name="priority-up-down" id="priority-up-down" value="升冪"></td>
                                <td class="usertable">詳細內容</td>
                            </tr>
                            <?php
                                $data=mysqli_query($db,"SELECT*FROM `todo`");
                                @$numberud=$_GET["num-up-down"];
                                @$titleud=$_GET["title-up-down"];
                                @$timeud=$_GET["time-up-down"];
                                @$dateud=$_GET["date-up-down"];
                                @$dealud=$_GET["deal-up-down"];
                                @$priorityud=$_GET["priority-up-down"];
                                if($numberud=="升冪"){
                                    down($data,"id");
                                    ?><script>document.getElementById("num-up-down").value="降冪";</script><?php
                                }elseif($titleud=="升冪"){
                                    down($data,"title");
                                    ?><script>document.getElementById("title-up-down").value="降冪";</script><?php
                                }elseif($timeud=="升冪"){
                                    down($data,"date");
                                    ?><script>document.getElementById("time-up-down").value="降冪";</script><?php
                                }elseif($dateud=="升冪"){
                                    down($data,"start_time");
                                    ?><script>document.getElementById("date-up-down").value="降冪";</script><?php
                                }elseif($dealud=="升冪"){
                                    down($data,"deal");
                                    ?><script>document.getElementById("deal-up-down").value="降冪";</script><?php
                                }elseif($priorityud=="升冪"){
                                    down($data,"priority");
                                    ?><script>document.getElementById("priority-up-down").value="降冪";</script><?php
                                }elseif(isset($numberud)||isset($titleud)||isset($timeud)||isset($dateud)||isset($dealud)||isset($priorityud)){
                                    header("location:userWelcome.php");
                                }else{
                                    up($data,"id");
                                }
                            ?>
                        </form>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="todo">
                    <class>TODO工作表</class>
                    <table>
                        <tr>
                            <td class="todo-title-time">時間</td>
                            <td class="todo-title-work">工作計畫</td>
                        </tr>
                        <?php
                            $m=0;
                            if($start=="升冪"){
                                for($i=0;$i<=22;$i=$i+2){
                                    ?>
                                    <tr>
                                        <td class="todo-title-main<?= $m%2+1; ?>"><?php echo(str_pad($i,2,"0",STR_PAD_LEFT)."~".str_pad($i+2,2,"0",STR_PAD_LEFT)); ?></td>
                                        <td class="todo-main<?= $m%2+1; ?>" id="<?= $m+1 ?>">
                                            <div id="<?= $i ?>up" class="upusertablediv"></div>
                                            <div id="<?= $i+0.5 ?>up" class="upusertablediv"></div>
                                            <div id="<?= $i+1 ?>up" class="userhalf upusertablediv"></div>
                                            <div id="<?= $i+1.5 ?>up" class="upusertablediv"></div>
                                        </td>
                                    </tr>
                                    <?php
                                    $m=$m+1;
                                }
                            }else{
                                for($i=22;$i>=0;$i=$i-2){
                                    ?>
                                    <tr>
                                        <td class="todo-title-main<?= $m%2+1; ?>"><?= str_pad($i+2,2,"0",STR_PAD_LEFT)."~".str_pad($i,2,"0",STR_PAD_LEFT); ?></td>
                                        <td class="todo-main<?= $m%2+1; ?>">
                                            <div id="<?= $i+2; ?>down" class="downusertablediv"></div>
                                            <div id="<?= $i+1.5; ?>down" class="downusertablediv"></div>
                                            <div id="<?= $i+1; ?>down" class="userhalf downusertablediv"></div>
                                            <div id="<?= $i+0.5; ?>down" class="downusertablediv"></div>
                                        </td>
                                    </tr>
                                    <?php
                                    $m=$m+1;
                                }
                            }
                        ?>
                    </table>
                </td>
                <td class="user-table4">
                    <form>
                        開始時間: <input type="submit" name="starttime" value="<?= $start ?>" class="table4but" id="updownbut"><br>
                        處理情形:
                        <select class="table4but" name="deal-select">
                            <option>篩選器</option>
                            <option>未處理</option>
                            <option>處理中</option>
                            <option>已完成</option>
                        </select><br>
                        優先情形:
                        <select class="table4but" name="priority-select">
                            <option>篩選器</option>
                            <option>普通</option>
                            <option>速件</option>
                            <option>最速件</option>
                        </select><br><br>
                        <button type="submit" class="right" name="selecter">確定(篩選器)</button>
                        <button type="button" class="newtodo" onclick="location.href='useradd.php'">新增todo</button><br><br>
                        <button type="button" id="setting-button" class="setting-button" onclick="location.href='setting.php'">setting</button>
                        <button type="submit" id="loggout-button" class="loggout-button" name="logout">logout</button>
                        <button type="button" id="user-button" class="user-button">用戶</button>
                    </form>
                    <?php
                        if(isset($_GET["preview"])){
                            $id=$_GET["preview"];
                            @$row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `todo` WHERE `id`='$id'"));
                            ?>
                            <div class="div">
                                標題: <?= @$row[1]; ?><br>
                                詳細內容: <?= @$row[7]; ?>
                                <button onclick="location.href='userWelcome.php'" id="button4">關閉</button>
                            </div>
                            <?php
                        }
                    ?>
                </td>
            </tr>
        </table>
        <form>
            <?php
                if(isset($_SESSION["priority"])||isset($_SESSION["deal"])){
                    @$priority=$_SESSION["priority"];
                    @$deal=$_SESSION["deal"];
                    $todo_conditions="`date`='$date'";
                    if($deal!="篩選器"){
                        $todo_conditions=$todo_conditions." AND `deal`='$deal'";
                    }
                    if($priority!="篩選器"){
                        $todo_conditions=$todo_conditions." AND `priority`='$priority'";
                    }
                    $todo=mysqli_query($db,"SELECT*FROM `todo` WHERE $todo_conditions");
                    if($start=="升冪"){
                        uper($todo);
                    }else{
                        lower($todo);
                    }
                }else{
                    $todo=mysqli_query($db, "SELECT*FROM `todo` WHERE `date`='$date'");
                    if($start=="升冪"){
                        uper($todo);
                    }else{
                        lower($todo);
                    }
                }
                @$user_data=$_SESSION["data"];
                if(isset($_GET["logout"])){
                    $user=mysqli_query($db,"SELECT*FROM `user` WHERE `userNumber`='$user_data'");
                    $row=mysqli_fetch_row($user);
                    if(isset($user_data)){
                        mysqli_query($db,"UPDATE `data` SET `logouttime`='$time' WHERE `usernumber`='$user_data' AND `logouttime`=''");
                        mysqli_query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)
                        VALUES('$user_data','$row[1]','$row[2]','$row[3]','一般使用者','-','-','登出','$time')");
                        ?><script>alert("登出成功!");location.href="index.php"</script><?php
                        session_unset();
                    }else{
                        mysqli_query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)
                        VALUES('null','','','','','','','登出','$time')");
                        ?><script>alert("登出成功!");location.href="index.php"</script><?php
                        session_unset();
                    }
                }
                if(isset($_GET["edit"])){
                    unset($_SESSION["todoval"]);
                    $_SESSION["todoval"]=$_GET["edit"];
                    ?><script>location.href="useradd.php"</script><?php
                }
                if(isset($_GET["selecter"])){
                    $_SESSION["priority"]=$_GET["priority-select"];
                    $_SESSION["deal"]=$_GET["deal-select"];
                    ?><script>location.href="userWelcome.php"</script><?php
                }
                if(isset($_GET["starttime"])){
                    if($_GET["starttime"]=="升冪"){
                        $_SESSION["starttime"]="降冪";
                    }else{
                        $_SESSION["starttime"]="升冪";
                    }
                    ?><script>location.href="userWelcome.php"</script><?php
                }
                if(isset($_GET["enter"])){
                    @$_SESSION["date"]=$_GET["date"];
                    ?><script>location.href="userWelcome.php"</script><?php
                }
            ?>
        </form>
        <script src="todobox.js"></script>
    </body>
</html>