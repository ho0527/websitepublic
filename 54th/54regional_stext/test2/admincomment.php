<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>快樂旅遊網</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["issignin"])){ header("location: signin.php"); }
        ?>
        <div class="nav tr" id="">
            <div></div>
            <div class="navdiv">
                <input type="button" class="btn-primary" id="index.php" onclick="location.href='index.php'" value="首頁">
                <input type="button" class="btn-primary" id="comment.php" onclick="location.href='comment.php'" value="訪客留言">
                <input type="button" class="btn-primary" id="bookroom.php" onclick="location.href='bookroom.php'" value="訪客訂房">
                <input type="button" class="btn-primary" id="orderfood.php" onclick="location.href='orderfood.php'" value="訪客訂餐">
                <input type="button" class="btn-primary" id="info.php" onclick="location.href='info.php'" value="交通資訊">
                <input type="button" class="btn-primary" id="signin.php" onclick="location.href='api.php?signout='" value="登出">
            </div>
        </div>
        <div class="nav2 tr" id="">
            <input type="button" class="btn-outline-primary active" id="admincomment.php" onclick="location.href='admincomment.php'" value="留言管理">
            <input type="button" class="btn-outline-primary" id="adminbookroom.php" onclick="location.href='adminbookroom.php'" value="訂房管理">
            <input type="button" class="btn-outline-primary" id="admin.php" onclick="location.href='adminorderfood.php'" value="訂餐管理">
        </div>

        <div class="top100px">
            <?php
                $row=query($db,"SELECT*FROM `comment` ORDER BY `id` DESC");
                for($i=0;$i<count($row);$i=$i+1){
                    $id=$row[$i]["id"];
                    $image=$row[$i]["image"];
                    $name=$row[$i]["name"];
                    $content=$row[$i]["content"];
                    $email=$row[$i]["email"];
                    $emailshow=$row[$i]["emailshow"];
                    $phone=$row[$i]["phone"];
                    $phoneshow=$row[$i]["phoneshow"];
                    $code=$row[$i]["code"];
                    $connent="";
                    $reply="管理員回應: ".$row[$i]["reply"];
                    $time="發表於 ".$row[$i]["createtime"];
                    $createtime=$row[$i]["createtime"];
                    $updatetime=$row[$i]["updatetime"];
                    $deletetime=$row[$i]["deletetime"];
                    $codecontent="訂選";
                    if($row[$i]["pin"]=="true"){
                        $codecontent="取消訂選";
                    }
                    ?>
                    <div class="commentdiv">
                        <div class="name"><?= $name ?></div>
                        <div class="image"><img src="<?= $image ?>" class="image"></div>
                        <div class="content"><?= $content ?></div>
                        <div class="time"><?= $time ?></div>
                        <div class="connent"><?= $connent ?></div>
                        <div class="reply"><?= $reply ?></div>
                        <div class="function">
                            <input type="button" class="editbutton" onclick="location.href='reply.php?id=<?= $id ?>'" value='管理員回應'>
                            <input type="button" class="editbutton" onclick="location.href='api.php?pincomment=<?= $id ?>'" value='<?= $codecontent ?>'>
                            <input type="button" class="editbutton" onclick="location.href='editcomment.php?id=<?= $id ?>'" value='編輯'>
                            <input type="button" class="deletebutton" onclick="location.href='api.php?admindeletecomment=<?= $id ?>'" value='刪除'>
                        </div>
                    </div>
                    <?php
                }
            ?>
        </div>

        <script src="init.js"></script>
        <script src="signin.js"></script>
    </body>
</html>