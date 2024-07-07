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
        <div class="nav tr" id="adminnav">
            <div class="title">網站管理 - 留言管理</div>
        </div>
        <div class="nav2 tc">
            <div class="btn-group">
                <input type="button" class="btn btn-outline-light active" onclick="location.href='admincomment.php'" value="留言管理">
                <input type="button" class="btn btn-outline-light" onclick="location.href='adminbookroom.php'" value="訂房管理">
                <input type="button" class="btn btn-outline-light" onclick="location.href='adminorderfood.php'" value="訂餐管理">
                <input type="button" class="btn btn-outline-light" onclick="location.href='adminfood.php'" value="餐點管理">
            </div>
        </div>

        <div class="commentmain">
            <?php
                $row=query($db,"SELECT*FROM `comment` ORDER BY `pin` DESC,`id` DESC");
                for($i=0;$i<count($row);$i=$i+1){
                    $pin="";
                    $username=$row[$i]["username"];
                    $comment="<img src='".$row[$i]["image"]."' class='image commentimg'>".$row[$i]["content"];
                    $time="發表於: ".$row[$i]["createtime"];
                    $reply="管理員回應: ".$row[$i]["reply"];

                    if($row[$i]["pin"]==""){
                        $pincontent="置頂";
                    }else{
                        $pincontent="取消置頂";
                        $pin="pin";
                    }

                    $connect=($row[$i]["emailshow"]=="true")?("Email: ".$row[$i]["email"]):("Email: 不顯示");
                    $connect=$connect."，".(($row[$i]["phoneshow"]=="true")?("連絡電話: ".$row[$i]["phone"]):("連絡電話: 不顯示"));

                    if($row[$i]["deletetime"]!=""){
                        $time=$time."，刪除於 ".$row[$i]["deletetime"];
                        $comment="";
                        $connect="";
                        $reply="";
                    }elseif($row[$i]["updatetime"]!=""){
                        $time=$time."，修改於 ".$row[$i]["updatetime"];
                    }
                    ?>
                    <div class="commentdiv <?= $pin ?>">
                        <div class="commentusername"><?= $username ?></div>
                        <div class="commentmid">
                            <div class="commentcomment"><?= $comment ?></div>
                            <div class="commenttime"><?= $time ?></div>
                            <div class="commentconnect"><?= $connect ?></div>
                            <div class="commentreply"><?= $reply ?></div>
                        </div>
                        <div class="commentfunction admincommentfunction">
                            <input type="button" class="btn btn-outline-dark fill" onclick="location.href='api.php?pincomment=<?= $row[$i]['id'] ?>'" value="<?= $pincontent ?>">
                            <input type="button" class="btn btn-outline-dark fill" onclick="location.href=' replycomment.php?id=<?= $row[$i]['id'] ?>'" value="回應">
                            <input type="button" class="btn btn-outline-dark fill" onclick="location.href=' editcomment.php?editcomment=<?= $row[$i]['id'] ?>'" value="修改">
                            <input type="button" class="btn btn-outline-danger fill" onclick="location.href='api.php?admindeletecomment=<?= $row[$i]['id'] ?>'" value="刪除">
                        </div>
                    </div>
                    <?php
                }
            ?>
        </div>

        <script src="init.js"></script>
    </body>
</html>