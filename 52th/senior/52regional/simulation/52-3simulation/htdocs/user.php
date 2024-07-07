<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>網站前台登入介面</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <table>
        <tr>
            <td class="date">
                <form>
                <?php
                    include("link.php");
                    include("userdef.php");
                    unset($_SESSION["todoval"]);    
                    echo($_SESSION["date"]);
                ?>
                <input type="date" value="<?= $_SESSION["date"]; ?>" name="date"> 
                    <input type="submit" name="datesubmit" value="確定(篩選器)"> 
            </form>

            </td>
            <td class="title">一般會員專區</td>
            <td rowspan="2"></td>
        </tr>
        <tr>
            <td class="todo">
                <table id="todo">
                TODO工作表
                <tr>
                    <td class="usernum2">時間</td>
                    <td class="user2">工作計畫</td>
                </tr>
                <?php
                    $start=$_SESSION["starttime"];
                    $num=0;
                    if($start=="升冪"){
                        for($i=0;$i<=22;$i+=2){
                            ?>
                            <tr>
                                <td class="usernum<?= $num%2+1 ?>"><?= str_pad($i,2,"0",STR_PAD_LEFT)."~".str_pad($i+2,2,"0",STR_PAD_LEFT)  ?></td>
                                <td class="user<?= $num%2+1 ?>">
                                    <div id="<?= $i ?>" class="useruptablediv"></div>
                                    <div id="<?= $i+0.5 ?>" class="useruptablediv"></div>
                                    <div id="<?= $i+1 ?>" class="userhelf useruptablediv"></div>
                                    <div id="<?= $i+1.5 ?>" class="useruptablediv"></div>
                                </td>    
                            </tr>
                            <?php
                            $num=$num+1;
                        }
                    }else{
                        for($i=22;$i>=0;$i-=2){
                            ?>
                            <tr>
                                <td class="usernum<?= $num%2+1 ?>"><?= str_pad($i+2,2,"0",STR_PAD_LEFT)."~".str_pad($i,2,"0",STR_PAD_LEFT)  ?></td>
                                <td class="user<?= $num%2+1 ?>">
                                    <div id="<?= $i+2 ?>" class="userdowntablediv"></div>
                                    <div id="<?= $i+1.5 ?>" class="userdowntablediv"></div>
                                    <div id="<?= $i+1 ?>" class="userhelf userdowntablediv"></div>
                                    <div id="<?= $i+0.5 ?>" class="userdowntablediv"></div>
                                </td>    
                            </tr>
                            <?php
                            $num=$num+1;
                        }
                    }
                ?>
</table>
            </td>
            <td>
                <form>
                    開始時間<input type="submit" name="updown" value="<?= $start ?>"><br>
                    處理情形:
                    <select name="deal">
                        <option>篩選器</option>
                        <option>未處理</option>
                        <option>處理中</option>
                        <option>已完成</option>
                    </select><br>
                    優先情形:
                    <select name="prot">
                        <option>篩選器</option>
                        <option>普通</option>
                        <option>速件</option>
                        <option>最速件</option>
                    </select><br>
                    <input type="submit" name="submit" value="確定(篩選器)"><br>
                    <input type="submit" name="new" value="新增"><br>   
                    <input type="submit" name="logout" value="logout">
                </form>
            </td>
        </tr>
        <?php
        if(isset($_GET["submit"])){
            @$priority=$_GET["prot"];
            @$deal=$_GET["deal"];
            $date=$_SESSION["date"];
            $todo="`date`='$date'";
            if($deal!="篩選器"){
                $todo=$todo." AND `deal`='$deal'";
            }
            if($priority!="篩選器"){
                $todo=$todo." AND `prot`='$priority'";
            }
            $todo=mysqli_query($db,"SELECT*FROM `todo` WHERE $todo");
            if($start=="升冪"){
                uper($todo);
            }else{
                lower($todo);
            }
        }else{
            $date=$_SESSION["date"];
            $todo=mysqli_query($db, "SELECT*FROM `todo` WHERE `date`='$date'");
            if($start=="升冪"){
                uper($todo);
            }else{
                lower($todo);
            }
        }
    if(isset($_GET["updown"])){
        $start=$_SESSION["starttime"];
        if($start=="升冪"){
            $_SESSION["starttime"]="降冪";
        }else{
            $_SESSION["starttime"]="升冪";
        }
        ?><script>location.href='user.php'</script><?php
    }
    if(isset($_GET["datesubmit"])){
        $_SESSION["date"]=$_GET["date"];
        ?><script>location.href='user.php'</script><?php
    }
    if(isset($_GET["new"])){
        ?><script>location.href='useradd.php'</script><?php
        
    }
    if(isset($_GET["logout"])){
        session_unset();
        ?><script>location.href='index.php'</script><?php
    }
    if(isset($_GET["edit"])){
        $_SESSION["todoval"]=$_GET["edit"];
        ?><script>location.href="useradd.php"</script><?php
    }
    if(isset($_GET["preview"])){
        $id=$_GET["preview"];
        @$row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `todo` WHERE `id`='$id'"));
        ?>
        <div class="div">
            標題: <?= @$row[1]; ?><br>
            詳細內容: <?= @$row[7]; ?>
            <button onclick="location.href='user.php'">關閉</button>
        </div>
        <?php
    }
        ?>
</table>

<script src="user.js"></script>
</body>
</html>