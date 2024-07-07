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
            if(!isset($_SESSION["signin"])){ header("location:admincomment.php"); }
        ?>
        <div class="nav" id="adminnav">
            <div class="title">網站管理 - 留言管理</div>
        </div>
        <div class="nav2 center">
            <div class="btn-group">
                <input type="button" class="btn btn-outline-light active" onclick="location.href='admincomment.php'" value="留言管理">
                <input type="button" class="btn btn-outline-light" onclick="location.href='adminbookroom.php'" value="訂房管理">
                <input type="button" class="btn btn-outline-light" onclick="location.href='adminorderfood.php'" value="訂餐管理">
            </div>
        </div>

        <div class="commentmain">
            <?php
                $row=query("SELECT*FROM `comment` ORDER BY `pin` DESC,`id` DESC");
                for($i=0;$i<count($row);$i=$i+1){
                    ?>
                    <div class="commentdiv <?= $row[$i]["pin"]=="true"?"pin":""; ?>">
                        <div class="commentdivname center"><?= $row[$i]["name"]; ?></div>
                        <div class="commentdivmid">
                            <div class="commentdivcontent"><?= $row[$i]["deletetime"]==""?("<img src='".$row[$i]["image"]."' class='image'>".$row[$i]["content"]):""; ?></div>
                            <div class="commentdivreply"><?= "管理員回應: ".($row[$i]["deletetime"]==""?($row[$i]["reply"]):""); ?></div>
                            <div class="commentdivtime"><?= "發表於 ".$row[$i]["createtime"].(($row[$i]["deletetime"]=="")?(($row[$i]["updatetime"]=="")?(""):("，修改於 ".$row[$i]["updatetime"])):("，刪除於 ".$row[$i]["deletetime"])); ?></div>
                            <div class="commentdivconnent"><?= $row[$i]["deletetime"]==""?"Email: ".($row[$i]["emailshow"]=="true"?$row[$i]["email"]:"未顯示")."，連絡電話: ".($row[$i]["phoneshow"]=="true"?$row[$i]["phone"]:"未顯示"):""; ?></div>
                        </div>
                        <div class="commentdivfunction center">
                            <input type="button" class="btn btn-light editbutton fill" onclick="location.href='editcomment.php?id=<?= $row[$i][0]; ?>'" value="修改">
                            <input type="button" class="btn btn-light deletebutton fill" onclick="location.href='adminreply.php?id=<?= $row[$i][0]; ?>'" value="回應">
                            <input type="button" class="btn btn-light deletebutton fill" onclick="location.href='api.php?adminpincomment=<?= $row[$i][0]; ?>'" value="<?= $row[$i]["pin"]=="true"?"解":""; ?>置頂">
                            <input type="button" class="btn btn-danger deletebutton fill" onclick="if(confirm('confirm?')){ location.href='api.php?admindeletecomment=<?= $row[$i][0]; ?>' }" value="刪除">
                        </div>
                    </div>
                    <?php
                }
            ?>
        </div>

        <script src="init.js"></script>
    </body>
</html>