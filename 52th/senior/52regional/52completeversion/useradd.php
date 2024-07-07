<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>新增工作表</title>
        <link href="index.css" rel="Stylesheet">
    </head>
    <body>
        <class class="useradd">
            <?php
                include("link.php");
                if(isset($_SESSION["todoval"])){
                    $id=$_SESSION["todoval"];
                    $todo=mysqli_query($db,"SELECT*FROM `todo` WHERE `id`='$id'");
                    $row=mysqli_fetch_row($todo);
                    ?>
                    <form>
                        <div class="indextitle">工作修改</div>
                        id: <input type="text" name="id" value="<?= $id; ?>" id="title" readonly><br>
                        工作標題: <input type="text" name="title" value="<?= $row[1]; ?>" id="title"><br>
                        日期: <input type="date" value="<?= $row[2]; ?>" name="date"><br>
                        開始時間:
                        <select name="start-hr">
                            <option>hr</option>
                            <?php
                                for($i=0;$i<24;$i=$i+1){
                                    if(substr($row[3],0,2)==$i){
                                        ?>
                                        <option selected><?php echo(str_pad($i,2,"0",STR_PAD_LEFT)); ?></option>
                                        <?php
                                    }else{
                                        ?>
                                        <option><?php echo(str_pad($i,2,"0",STR_PAD_LEFT)); ?></option>
                                        <?php
                                    }
                                }
                            ?>
                        </select>
                        <select name="start-min">
                            <?php
                            if(substr($row[3],3,5)=="00"){
                                ?>
                                <option>min</option>
                                <option selected>00</option>
                                <option>30</option>
                                <?php
                            }else{
                                ?>
                                <option>min</option>
                                <option>00</option>
                                <option selected>30</option>
                                <?php
                            }
                            ?>
                        </select><br>
                        結束時間:
                        <select name="end-hr">
                            <option>hr</option>
                            <?php
                                for($i=0;$i<24;$i=$i+1){
                                    if(substr($row[4],0,2)==$i){
                                        ?>
                                        <option selected><?php echo(str_pad($i,2,"0",STR_PAD_LEFT)); ?></option>
                                        <?php
                                    }else{
                                        ?>
                                        <option><?php echo(str_pad($i,2,"0",STR_PAD_LEFT)); ?></option>
                                        <?php
                                    }
                                }
                            ?>
                        </select>
                        <select name="end-min">
                            <?php
                            if(substr($row[4],3,5)=="00"){
                                ?>
                                <option>min</option>
                                <option selected>00</option>
                                <option>30</option>
                                <?php
                            }else{
                                ?>
                                <option>min</option>
                                <option>00</option>
                                <option selected>30</option>
                                <?php
                            }
                            ?>
                        </select><br>
                        處理情形:
                        <select name="deal" id="deal">
                            <?php
                                if($row[5]=="未處理"){
                                    ?>
                                    <option selected>未處理</option>
                                    <option>處理中</option>
                                    <option>已完成</option>
                                    <?php
                                }elseif($row[5]=="處理中"){
                                    ?>
                                    <option>未處理</option>
                                    <option selected>處理中</option>
                                    <option>已完成</option>
                                    <?php
                                }else{
                                    ?>
                                    <option>未處理</option>
                                    <option>處理中</option>
                                    <option selected>已完成</option>
                                    <?php
                                }
                            ?>
                        </select>
                        類別:
                        <select name="priority" id="priority">
                            <?php
                                if($row[6]=="普通"){
                                    ?>
                                    <option selected>普通</option>
                                    <option>速件</option>
                                    <option>最速件</option>
                                    <?php
                                }elseif($row[6]=="速件"){
                                    ?>
                                    <option>普通</option>
                                    <option selected>速件</option>
                                    <option>最速件</option>
                                    <?php
                                }else{
                                    ?>
                                    <option>普通</option>
                                    <option>速件</option>
                                    <option selected>最速件</option>
                                    <?php
                                }
                            ?>
                        </select><br>
                        <textarea rows="7" cols="25" placeholder="詳細敘述工作內容" name="detaile" id="detaile"><?= $row[7]; ?></textarea><br>
                        <input type="submit" id="cancel-but" name="cancel-but" class="button" value="取消">
                        <button tyep="submit" id="finish-but" name="finish-but" class="button" value="完成">完成</button>
                        <input type="button" id="del-but" class="button" value="刪除"><br><br>
                        <div id="confirm">
                            確定刪除?
                            <input type="submit" name="confirm-but" class="button" value="確定">
                            <input type="button" name="no-but" class="button" value="取消" onclick="location.href='useradd.php'"><br>
                        </div>
                        <script src="todaydate.js"></script>
                    </form><br>
                    <?php
                }else{
                    ?>
                    <form>
                        <div class="indextitle">新增工作</div>
                        工作標題:<input type="text" name="title" value="work" id="title"><br>
                        日期: <input type="date" value="<?= $_SESSION["date"] ?>" name="date"><br>
                        開始時間:
                        <select name="start-hr">
                            <option>hr</option>
                            <?php
                                for($i=0;$i<24;$i=$i+1){
                                    ?>
                                    <option><?php echo(str_pad($i,2,"0",STR_PAD_LEFT)); ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                        <select name="start-min">
                            <option>min</option>
                            <option>00</option>
                            <option>30</option>
                        </select><br>
                        結束時間:
                        <select name="end-hr">
                            <option>hr</option>
                            <?php
                                for($i=0;$i<24;$i=$i+1){
                                    ?>
                                    <option><?php echo(str_pad($i,2,"0",STR_PAD_LEFT)); ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                        <select name="end-min">
                            <option>min</option>
                            <option>00</option>
                            <option>30</option>
                        </select><br>
                        處理情形:
                        <select name="deal" id="deal">
                            <option>未處理</option>
                            <option>處理中</option>
                            <option>已完成</option>
                        </select>
                        類別:
                        <select name="priority" id="priority">
                            <option>普通</option>
                            <option>速件</option>
                            <option>最速件</option>
                        </select><br>
                        <textarea rows="10" cols="25" placeholder="詳細敘述工作內容" name="detaile" id="detaile"></textarea><br>
                        <input type="submit" id="cancel-but" name="cancel-but" class="button" value="取消">
                        <button tyep="submit" id="finish-but" name="finish-but" class="button" value="完成">完成</button>
                        <script src="todaydate.js"></script>
                    </form><br><br>
                    <?php
                }
                if(isset($_GET["finish-but"])){
                    $title=$_GET['title'];
                    $date=$_GET["date"];
                    $starthr=$_GET["start-hr"];
                    $startmin=$_GET["start-min"];
                    $endhr=$_GET["end-hr"];
                    $endmin=$_GET["end-min"];
                    $deal=$_GET["deal"];
                    $priority=$_GET["priority"];
                    $detail=$_GET["detaile"];
                    $start=($starthr.":".$startmin);
                    $end=($endhr.":".$endmin);
                    $todo=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `todo` WHERE `title`='$title'"));
                    if($starthr=="hr"||$startmin=="min"||$endhr=="hr"||$endmin=="min"||$title==""){
                        ?><script>alert("請填寫時間/標題");location.href="useradd.php"</script><?php
                    }elseif($start>=$end){
                        ?><script>alert("時間填寫錯誤");location.href="useradd.php"</script><?php
                    }else{
                        if(isset($id)){
                            if($row&&$row[0]!=$_SESSION["todoval"]){
                                ?><script>alert("工作表已存在");location.href="useradd.php"</script><?php
                            }else{
                                mysqli_query($db,"UPDATE `todo` SET `title`='$title', `date`='$date', `start_time`='$start',`end_time`='$end', `deal`='$deal', `priority`='$priority', `detail`='$detail' WHERE `id`='$id'");
                                ?><script>location.href="userWelcome.php"</script><?php
                                unset($id);
                            }
                        }else{
                            if($row){
                                ?><script>alert("工作表已存在");location.href="useradd.php"</script><?php
                            }else{
                                mysqli_query($db,"INSERT INTO `todo`(`title`, `date`, `start_time`, `end_time`, `deal`, `priority`, `detail`)
                                VALUES('$title','$date','$start','$end','$deal','$priority','$detail')");
                                ?><script>location.href="userWelcome.php"</script><?php
                            }
                        }
                    }
                }
                if(isset($_GET["cancel-but"])){
                    ?><script>location.href="userWelcome.php"</script><?php
                    unset($id);
                }
                if(isset($_GET["confirm-but"])){
                    mysqli_query($db,"DELETE FROM `todo` WHERE `id`='$id'");
                    ?><script>location.href="userWelcome.php"</script><?php
                    unset($id);
                }
            ?>
        </class>
        <script src="add.js"></script>
    </body>
</html>