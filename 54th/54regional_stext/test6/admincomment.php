<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["signin"])){ header("location: signin.php"); }
        ?>
        <div class="nav" id="adminnav">
            <div class="title">網站管理 - 留言管理</div>
        </div>
        <div class="nav2">
            <div class="btn-group">
                <a href="admincomment.php" class="btn btn-outline-light">留言管理</a>
                <a href="adminbookroom.php" class="btn btn-outline-light">訂房管理</a>
                <a href="adminorderfood.php" class="btn btn-outline-light">訂餐管理</a>
            </div>
        </div>

        <div class="commentmain">
            <?php
                $row=query("SELECT*FROM `comment` ORDER BY `pin` DESC, `id` DESC");
                for($i=0;$i<count($row);$i=$i+1){
                    ?>
                    <div class="commentdiv <?= $row[$i]["pin"] ?>">
                        <div class="commentdivname"><?= $row[$i]["name"] ?></div>
                        <div class="commentdivmid">
                           <div class="commentdivcontent"><?= (($row[$i]["deletetime"]=="")?("<img src='".$row[$i]["image"]."' class='image'>".$row[$i]["content"]):("")) ?></div>
                           <div class="commentdivreply"><?= (($row[$i]["deletetime"]=="")?("管理員回應: ".$row[$i]["reply"]):("")) ?></div>
                           <div class="commentdivtime"><?= ("發表於 ".$row[$i]["createtime"]).(($row[$i]["deletetime"]=="")?(($row[$i]["updatetime"]=="")?(""):("，修改於 ".$row[$i]["updatetime"])):("，刪除於 ".$row[$i]["deletetime"])) ?></div>
                           <div class="commentdivconntent"><?= (($row[$i]["deletetime"]=="")?((($row[$i]["emailshow"]=="")?("Email: 未顯示"):("Email: ".$row[$i]["email"]))."，".(($row[$i]["phoneshow"]=="")?("連絡電話: 未顯示"):("連絡電話: ".$row[$i]["phone"]))):("")) ?></div>
                        </div>
                        <div class="commentdivfunction">
                            <input type='button' class='btn btn-light edit' onclick="location.href='adminreply.php?id=<?= $row[$i]['id'] ?>'" value='回應'>
                            <input type='button' class='btn btn-light edit' onclick="location.href='api.php?adminpincomment=<?= $row[$i]['id'] ?>'" value='<?= ($row[$i]["pin"]=="")?"":"解" ?>置頂'>
                            <input type='button' class='btn btn-light edit' onclick="location.href='admineditcomment.php?id=<?= $row[$i]['id'] ?>'" value='修改'>
                            <input type='button' class='btn btn-danger del' onclick="location.href='api.php?admindeletecomment=<?= $row[$i]['id'] ?>'" value='刪除'>
                        </div>
                    </div>
                    <?php
                }
            ?>
        </div>

        <script src="init.js"></script>
    </body>
</html>