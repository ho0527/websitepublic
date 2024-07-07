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
        ?>
        <div class="nav tr" id="nav">
            <div class="title">訪客留言版</div>
        </div>
        <div class="nav2 tc">
            <div class="title2">訪客留言列表</div>
            <input type="button" class="btn btn-warning" onclick="location.href='newcomment.php'" value="新增留言">
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
                    $function="
                        <input type=\"text\" id='".$row[$i]['id']."' data-code='".$row[$i]['code']."' placeholder='留言序號'>
                        <input type='button' class='btn btn-outline-dark edit' data-id='".$row[$i]["id"]."' value='修改'>
                        <input type='button' class='btn btn-outline-danger delete' data-id='".$row[$i]["id"]."' value='刪除'>
                    ";

                    $connect=($row[$i]["emailshow"]=="true")?("Email: ".$row[$i]["email"]):("Email: 不顯示");
                    $connect=$connect."，".(($row[$i]["phoneshow"]=="true")?("連絡電話: ".$row[$i]["phone"]):("連絡電話: 不顯示"));

                    if($row[$i]["pin"]=="true"){
                        $pin="pin";
                    }

                    if($row[$i]["deletetime"]!=""){
                        $time=$time."，刪除於: ".$row[$i]["deletetime"];
                        $comment="";
                        $connect="";
                        $reply="";
                        $function="已刪除";
                    }elseif($row[$i]["updatetime"]!=""){
                        $time=$time."，修改於: ".$row[$i]["updatetime"];
                    }
                    ?>
                    <div class="commentdiv <?= $pin ?>">
                        <div class="commentusername"><?= $username ?></div>
                        <div class="commentmid">
                            <div class="commentcomment"><?= $comment ?></div>
                            <div class="commentreply"><?= $reply ?></div>
                            <div class="commenttime"><?= $time ?></div>
                            <div class="commentconnect"><?= $connect ?></div>
                        </div>
                        <div class="commentfunction"><?= $function ?></div>
                    </div>
                    <?php
                }
            ?>
        </div>

        <script src="init.js"></script>
        <script src="comment.js"></script>
    </body>
</html>