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
            include("link.php")
        ?>
        <div class="nav tr" id="nav">訪客留言版</div>
        <div class="nav2" id="">
            訪客留言列表
            <input type="button" class="btn-primary" id="admin.php" onclick="location.href='newcomment.php'" value="新增留言">
        </div>

        <div class="top100px">
            <?php
                $row=query($db,"SELECT*FROM `comment` WHERE `pin`='true'");
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
                    $function="<input type=\"text\" id='$id' placeholder='留言序號'><input type=\"button\" class=\"editbutton\" data-id=\"$id\" data-code=\"$code\" value='編輯'><input type=\"button\" class=\"deletebutton\" data-id=\"$id\" data-code=\"$code\" value='刪除'>";

                    if($emailshow=="true"){
                        $connent="E-mail: ".$email;
                    }else{
                        $connent="E-mail: 未顯示";
                    }

                    if($phoneshow=="true"){
                        $connent=$connent."，電話: ".$phone;
                    }else{
                        $connent=$connent."，電話: 未顯示";
                    }

                    if($deletetime!=""){
                        $image="";
                        $content="";
                        $connent="";
                        $reply="";
                        $function="已刪除";
                        $time=$time."，刪除於 ".$row[$i]["deletetime"];
                    }elseif($updatetime!=""){
                        $time=$time."，修改於 ".$row[$i]["deletetime"];
                    }
                    ?>
                    <div class="commentdiv pin">
                        <div class="name"><?= $name ?></div>
                        <div class="image"><img src="<?= $image ?>" class="image"></div>
                        <div class="content"><?= $content ?></div>
                        <div class="time"><?= $time ?></div>
                        <div class="connent"><?= $connent ?></div>
                        <div class="reply"><?= $reply ?></div>
                        <div class="function"><?= $function ?></div>
                    </div>
                    <?php
                }

                $row=query($db,"SELECT*FROM `comment` WHERE `pin`='' ORDER BY `id` DESC");
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
                    $function="<input type=\"text\" id='$id' placeholder='留言序號'><input type=\"button\" class=\"editbutton\" data-id=\"$id\" data-code=\"$code\" value='編輯'><input type=\"button\" class=\"deletebutton\" data-id=\"$id\" data-code=\"$code\" value='刪除'>";

                    if($emailshow=="true"){
                        $connent="E-mail: ".$email;
                    }else{
                        $connent="E-mail: 未顯示";
                    }

                    if($phoneshow=="true"){
                        $connent=$connent."，電話: ".$phone;
                    }else{
                        $connent=$connent."，電話: 未顯示";
                    }

                    if($deletetime!=""){
                        $image="";
                        $content="";
                        $connent="";
                        $reply="";
                        $function="已刪除";
                        $time=$time."，刪除於 ".$row[$i]["deletetime"];
                    }elseif($updatetime!=""){
                        $time=$time."，修改於 ".$row[$i]["updatetime"];
                    }
                    ?>
                    <div class="commentdiv">
                        <div class="name"><?= $name ?></div>
                        <div class="image"><img src="<?= $image ?>" class="image"></div>
                        <div class="content"><?= $content ?></div>
                        <div class="time"><?= $time ?></div>
                        <div class="connent"><?= $connent ?></div>
                        <div class="reply"><?= $reply ?></div>
                        <div class="function"><?= $function ?></div>
                    </div>
                    <?php
                }
            ?>
        </div>

        <script src="init.js"></script>
        <script src="comment.js"></script>
    </body>
</html>