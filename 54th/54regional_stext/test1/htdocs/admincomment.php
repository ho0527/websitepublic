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
            if(!isset($_SESSION["islogin"])){ header("location: signup.php"); }
        ?>
        <div class="nav tr" id="admnav"></div>
        <div class="nav2 tc">
            <input type="button" class="btn-outline-primary active" onclick="location.href='admincomment.php'" value="留言管理">
            <input type="button" class="btn-outline-primary" onclick="location.href='adminbookroom.php'" value="訂房管理">
            <input type="button" class="btn-outline-primary" onclick="location.href='adminorderfood.php'" value="訂餐管理">
            <input type="button" class="btn-outline-primary" onclick="location.href='adminfood.php'" value="餐點管理">
        </div>

        <div class="commentmain">
            <?php
                $row=query($db,"SELECT*FROM `comment` ORDER BY `id` DESC");
                for($i=0;$i<count($row);$i=$i+1){
                    $image=$row[$i]["image"];
                    $username=$row[$i]["username"];
                    $comment=$row[$i]["content"];
                    $time="發表於: ".$row[$i]["createtime"];
                    $reply="管理員回應: ".$row[$i]["reply"];

                    if($row[$i]["pin"]==""){
                        $pin="置頂";
                    }else{
                        $pin="取消置頂";
                    }

                    if($row[$i]["emailshow"]=="true"){
                        $connect="Email: ".$row[$i]["email"];
                    }else{
                        $connect="Email: 不顯示";
                    }
                    if($row[$i]["phoneshow"]=="true"){
                        $connect=$connect."，連絡電話: ".$row[$i]["phone"];
                    }else{
                        $connect=$connect."，連絡電話: 不顯示";
                    }

                    if($row[$i]["deletetime"]!=""){
                        $time=$time."，刪除於: ".$row[$i]["deletetime"];
                    }elseif($row[$i]["updatetime"]!=""){
                        $time=$time."，修改於: ".$row[$i]["updatetime"];
                    }
                    ?>
                    <div class="commentdiv">
                        <div class="commentimage"><img src="<?= $image ?>" class="image"></div>
                        <div class="commentusername"><?= $username ?></div>
                        <div class="commentcomment"><?= $comment ?></div>
                        <div class="commenttime"><?= $time ?></div>
                        <div class="commentconnect"><?= $connect ?></div>
                        <div class="commentreply"><?= $reply ?></div>
                        <div class="commentfunction admincommentfunction">
                            <input type="button" class="fill" onclick="location.href='api.php?pincomment=<?= $row[$i]['id'] ?>'" value="<?= $pin ?>">
                            <input type="button" class="fill" onclick="location.href='replycomment.php?id=<?= $row[$i]['id'] ?>'" value="回應">
                            <input type="button" class="fill" onclick="location.href='editcomment.php?editcomment=<?= $row[$i]['id'] ?>'" value="修改">
                            <input type="button" class="fill" onclick="location.href='api.php?admindeletecomment=<?= $row[$i]['id'] ?>'" value="刪除">
                        </div>
                    </div>
                    <?php
                }
            ?>
        </div>

        <script src="init.js"></script>
    </body>
</html>