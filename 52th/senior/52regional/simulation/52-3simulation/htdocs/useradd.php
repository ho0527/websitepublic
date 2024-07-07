<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>新增工作表</title>
        <!-- <link href="index.css" rel="Stylesheet"> -->
    </head>
    <body>
        <?php
            include("link.php");
            if(isset($_SESSION["todoval"])){
                @$id=$_SESSION["todoval"];
                @$row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `todo` WHERE `id`='$id'"));
                ?>
                <form>
                    修改工作<br>
                    工作標題:<input type="text" name="title" value="work"><br>
                    日期: <input type="date" value="<?= $_SESSION["date"] ?>" name="date"><br>
                    開始時間:
                    <select name="starthr">
                        <?php
                            for($i=0;$i<24;$i=$i+1){
                                ?>
                                <option><?php echo(str_pad($i,2,"0",STR_PAD_LEFT)); ?></option>
                                <?php
                            }
                        ?>
                    </select>
                    <select name="startmin">
                        <option>00</option>
                        <option>30</option>
                    </select><br>
                    結束時間:
                    <select name="endhr">
                        <?php
                            for($i=0;$i<24;$i=$i+1){
                                ?>
                                <option><?php echo(str_pad($i,2,"0",STR_PAD_LEFT)); ?></option>
                                <?php
                            }
                        ?>
                    </select>
                    <select name="endmin">
                        <option>00</option>
                        <option>30</option>
                    </select><br>
                    處理情形:
                    <select name="deal">
                        <option>未處理</option>
                        <option>處理中</option>
                        <option>已完成</option>
                    </select>
                    優先情形:
                    <select name="prot">
                        <option>普通</option>
                        <option>速件</option>
                        <option>最速件</option>
                    </select><br>
                    <textarea placeholder="詳細敘述工作內容" name="deteil"></textarea><br>
                    <input type="button" name="cancelbut" onclick="location.href='user.php'" value="取消">
                    <button tyep="submit" name="finishbut" value="完成">完成</button>
                    <input type="button" id="del-but"  value="刪除"><br><br>
                    <div id="confirm">
                        確定刪除?
                        <input type="submit" name="confirmbut"  value="確定">
                        <input type="button" name="nobut"  value="取消" onclick="location.href='useradd.php'"><br>
                    </div>
                </form><br>
                <?php
            }else{
                ?>
                <form>
                    新增工作<br>
                    工作標題:<input type="text" name="title" value="work"><br>
                    日期: <input type="date" value="<?= $_SESSION["date"] ?>" name="date"><br>
                    開始時間:
                    <select name="starthr">
                        <?php
                            for($i=0;$i<24;$i=$i+1){
                                ?>
                                <option><?php echo(str_pad($i,2,"0",STR_PAD_LEFT)); ?></option>
                                <?php
                            }
                        ?>
                    </select>
                    <select name="startmin">
                        <option>00</option>
                        <option>30</option>
                    </select><br>
                    結束時間:
                    <select name="endhr">
                        <?php
                            for($i=0;$i<24;$i=$i+1){
                                ?>
                                <option><?php echo(str_pad($i,2,"0",STR_PAD_LEFT)); ?></option>
                                <?php
                            }
                        ?>
                    </select>
                    <select name="endmin">
                        <option>00</option>
                        <option>30</option>
                    </select><br>
                    處理情形:
                    <select name="deal">
                        <option>未處理</option>
                        <option>處理中</option>
                        <option>已完成</option>
                    </select>
                    優先情形:
                    <select name="prot">
                        <option>普通</option>
                        <option>速件</option>
                        <option>最速件</option>
                    </select><br>
                    <textarea placeholder="詳細敘述工作內容" name="deteil"></textarea><br>
                    <input type="button" id="cancelbut" name="cancelbut" onclick="location.href='user.php'" value="取消">
                    <button tyep="submit" id="finishbut" name="finishbut" value="完成">完成</button>
                </form><br><br>
                <?php
            }
            if(isset($_GET["finishbut"])){
                $title=$_GET['title'];
                $date=$_GET["date"];
                $starthr=$_GET["starthr"];
                $startmin=$_GET["startmin"];
                $endhr=$_GET["endhr"];
                $endmin=$_GET["endmin"];
                $deal=$_GET["deal"];
                $prot=$_GET["prot"];
                $deteil=$_GET["deteil"];
                $start=($starthr.":".$startmin);
                $end=($endhr.":".$endmin);
                if($start>=$end){
                    ?><script>alert("時間填寫錯誤");location.href="useradd.php"</script><?php
                }else{
                    echo(2);
                    if(isset($id)){
                        mysqli_query($db,"UPDATE `todo` SET `title`='$title', `date`='$date', `starttime`='$start',`endtime`='$end', `deal`='$deal', `prot`='$prot', `deteil`='$deteil' WHERE `id`='$id'");
                        ?><script>location.href="user.php"</script><?php
                    }else{
                        mysqli_query($db,"INSERT INTO `todo`(`title`, `date`, `starttime`, `endtime`, `deal`, `prot`, `deteil`)VALUES('$title','$date','$start','$end','$deal','$prot','$deteil')");
                        ?><script>location.href="user.php"</script><?php
                    }
                }
            }
            if(isset($_GET["confirmbut"])){
                mysqli_query($db,"DELETE FROM `todo` WHERE `id`='$id'");
                ?><script>location.href="user.php"</script><?php
            }
        ?>
        <script src="useradd.js"></script>
    </body>
</html>