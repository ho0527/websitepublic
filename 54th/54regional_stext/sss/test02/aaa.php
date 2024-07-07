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
            if(!isset($_SESSION["signin"])){ header("location:signin.php"); }
        ?>
        <div class="nav" id="adminnav">
            <div class="title">快樂旅遊網</div>
        </div>
        <div class="nav2">
            <div class="btn-group">
                <input type="button" class="btn btn-outline-light active" onclick="location.href='aaa.php'" value="aaa">
                <input type="button" class="btn btn-outline-light" onclick="location.href='bbb.php'" value="bbb">
                <input type="button" class="btn btn-outline-light" onclick="location.href='ccc.php'" value="ccc">
            </div>
        </div>

        <div class="commentmain">
            <?php
                $row=query("SELECT*FROM `comment` ORDER BY `pin` DESC,`id` DESC");
                for($i=0;$i<count($row);$i=$i+1){
                    ?>
                    <div class="commentdiv <?= $row[$i]["pin"] ?>">
                        <div class="commentdivuser center"><?= $row[$i]["name"] ?></div>
                        <div class="commentdivmid">
                            <div class="commentdivcontent"><?= ($row[$i]["deletetime"]=="")?"<img src='".$row[$i]["image"]."' class='image'>".$row[$i]["content"]:"" ?></div>
                            <div class="commentdivreply"><?= ($row[$i]["deletetime"]=="")?("管理員回應: ".$row[$i]["reply"]):"" ?></div>
                            <div class="commentdivtime">發表於 <?= $row[$i]["createtime"].(($row[$i]["deletetime"]=="")?((($row[$i]["updatetime"]=="")?(""):("，修改於 ".$row[$i]["updatetime"]))):("，刪除於 ".$row[$i]["deletetime"])) ?></div>
                            <div class="commentdivconntent"><?= ($row[$i]["deletetime"]=="")?($row[$i]["emailshow"]?("email:".$row[$i]["email"]):"email: 未顯示").(($row[$i]["phoneshow"]?("，phone:".$row[$i]["phone"]):"，phone: 未顯示")):"" ?></div>
                        </div>
                        <div class="commentdivfunction center">
                            <input type="button" class="btn btn-light editbutton fill" onclick="location.href='editc.php?id=<?= $row[$i][0] ?>'" value="修改">
                            <input type="button" class="btn btn-light deletebutton fill" onclick="location.href='reply.php?id=<?= $row[$i][0] ?>'" value="回應">
                            <input type="button" class="btn btn-light deletebutton fill" onclick="location.href='api.php?adminpincomment=<?= $row[$i][0] ?>'" value="<?= $row[$i]["pin"]=="pin"?"解":"" ?>置頂">
                            <input type="button" class="btn btn-danger deletebutton fill" onclick="if(confirm('confirm?')){ location.href='api.php?admindeletecomment=<?= $row[$i][0] ?>' }" value="刪除">
                        </div>
                    </div>
                    <?php
                }
            ?>   
        </div>

        <script src="init.js"></script>
    </body>
</html>