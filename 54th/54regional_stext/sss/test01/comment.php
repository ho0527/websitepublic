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
        <?php include("link.php"); ?>
        <div class="nav" id="nav">
            <div class="title">訪客留言版</div>
        </div>
        <div class="nav2 center" id="nav">
            <div class="title2">訪客留言列表</div>
            <input type="button" class="btn btn-warning" onclick="location.href='newcomment.php'" value="新增留言">
        </div>

        <div class="commentmain">
            <?php
                $row=query("SELECT*FROM `comment` ORDER BY `pin` DESC,`createtime` DESC");
                for($i=0;$i<count($row);$i=$i+1){
                    ?>
                    <div class="commentdiv <?= $row[$i]["pin"]==""?"":"pin" ?>">
                        <div class="commentdivname center"><?= $row[$i]["name"] ?></div>
                        <div class="commentdivmid">
                            <div class="commentdivcontent"><?= $row[$i]["deletetime"]==""?("<img src='".$row[$i]["image"]."' class='image'>".$row[$i]["content"]):""; ?></div>
                            <div class="commentdivreply"><?= ($row[$i]["deletetime"]==""?("管理員回應: ".$row[$i]["reply"]):""); ?></div>
                            <div class="commentdivtime"><?= "發表於 ".$row[$i]["createtime"].(($row[$i]["deletetime"]=="")?(($row[$i]["updatetime"]=="")?(""):("，修改於 ".$row[$i]["updatetime"])):("，刪除於 ".$row[$i]["deletetime"])); ?></div>
                            <div class="commentdivconnent"><?= $row[$i]["deletetime"]==""?"Email: ".($row[$i]["emailshow"]=="true"?$row[$i]["email"]:"未顯示")."，連絡電話: ".($row[$i]["phoneshow"]=="true"?$row[$i]["phone"]:"未顯示"):""; ?></div>
                        </div>
                        <div class="commentdivfunction center">
                            <?= $row[$i]["deletetime"]==""?"
                                <input type='text' class='input' id='".$row[$i]['id']."' placeholder='留言序號'>
                                <input type='button' class='btn btn-light editbutton' data-id='".$row[$i]['id']."' data-no='".$row[$i]['no']."' value='修改'>
                                <input type='button' class='btn btn-danger deletebutton' data-id='".$row[$i]['id']."' data-no='".$row[$i]['no']."' value='刪除'>
                            ":"已刪除"; ?>
                        </div>
                    </div>
                    <?php
                }
            
            ?>
        </div>

        <script src="init.js"></script>
        <script src="comment.js"></script>
    </body>
</html>