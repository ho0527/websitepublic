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
        <div class="nav tr" id="nav"></div>
        <div class="nav2 tc">
            訪客留言表 / 訪客留言列表
            <input type="button" onclick="location.href='newcomment.php'" value="新增留言">
        </div>

        <div class="commentmain">
            <?php
                $row=query($db,"SELECT*FROM `comment` WHERE `pin`='true'");
                for($i=0;$i<count($row);$i=$i+1){
                    $image=$row[$i]["image"];
                    $username=$row[$i]["username"];
                    $comment=$row[$i]["content"];
                    $time="發表於 ".$row[$i]["createtime"];
                    $reply="管理員回應: ".$row[$i]["reply"];
                    $function="
                        <input type=\"text\" id='".$row[$i]['id']."' data-code='".$row[$i]['code']."' placeholder='留言序號'>
                        <input type='button' class='edit' data-id='".$row[$i]["id"]."' value='修改'>
                        <input type='button' class='delete' data-id='".$row[$i]["id"]."' value='刪除'>
                    ";

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
                        $time=$time."，刪除於 ".$row[$i]["deletetime"];
                        $image="";
                        $comment="";
                        $connect="";
                        $reply="";
                        $function="已刪除";
                    }elseif($row[$i]["updatetime"]!=""){
                        $time=$time."，修改於 ".$row[$i]["updatetime"];
                    }
                    ?>
                    <div class="commentdiv pin">
                        <div class="commentimage"><img src="<?= $image ?>" class="image"></div>
                        <div class="commentusername"><?= $username ?></div>
                        <div class="commentcomment"><?= $comment ?></div>
                        <div class="commenttime"><?= $time ?></div>
                        <div class="commentconnect"><?= $connect ?></div>
                        <div class="commentreply"><?= $reply ?></div>
                        <div class="commentfunction"><?= $function ?></div>
                    </div>
                    <?php
                }

                $row=query($db,"SELECT*FROM `comment` WHERE `pin`='' ORDER BY `id` DESC");
                for($i=0;$i<count($row);$i=$i+1){
                    $image=$row[$i]["image"];
                    $username=$row[$i]["username"];
                    $comment=$row[$i]["content"];
                    $time="發表於: ".$row[$i]["createtime"];
                    $reply="管理員回應: ".$row[$i]["reply"];
                    $function="
                        <input type=\"text\" id='".$row[$i]['id']."' data-code='".$row[$i]['code']."' placeholder='留言序號'>
                        <input type='button' class='edit' data-id='".$row[$i]["id"]."' value='修改'>
                        <input type='button' class='delete' data-id='".$row[$i]["id"]."' value='刪除'>
                    ";

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
                        $image="";
                        $comment="";
                        $connect="";
                        $reply="";
                        $function="已刪除";
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