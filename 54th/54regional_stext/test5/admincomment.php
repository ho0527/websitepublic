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
                <a href="admincomment.php" class="btn btn-outline-light active">留言管理</a>
                <a href="adminbookroom.php" class="btn btn-outline-light">訂房管理</a>
                <a href="adminfoodorder.php" class="btn btn-outline-light">訂餐管理</a>
            </div>
        </div>

        <div class="commentmain">
            <?php
                $row=query("SELECT*FROM `comment` ORDER BY `pin` DESC,`id` DESC");
                foreach($row as $data){
                    ?>
                        <div class="commentdiv <?= $data["pin"] ?>">
                            <div class="commentdivname"><?= $data["name"] ?></div>
                            <div class="commentdivmid">
                                <div class="commentcontent"><?= (($data["deletetime"]=="")?("<img src='".$data["image"]."' class='image'>".$data["content"]):("")) ?></div>
                                <div class="commentreply"><?= (($data["deletetime"]=="")?("管理員回應: ".$data["reply"]):("")) ?></div>
                                <div class="commenttime"><?= ("發表於 ".$data["createtime"]).(($data["deletetime"]=="")?(($data["updatetime"]=="")?(""):("修改於 ".$data["updatetime"])):("刪除於 ".$data["deletetime"])) ?></div>
                                <div class="commentconnent"><?= (($data["deletetime"]=="")?((($data["emailshow"]=="")?("email: 不顯示"):("email: ".$data["email"]))."，".(($data["phoneshow"]=="")?("電話: 不顯示"):("電話: ".$data["phone"]))):("")) ?></div>
                            </div>
                            <div class="commentdivfunction">
                                <input type="button" class="btn btn-light edit" onclick="location.href='api.php?pincomment=<?= $data['id'] ?>'" value="<?= $data["pin"]?"解":"" ?>置頂">
                                <input type="button" class="btn btn-light edit" onclick="location.href='replycomment.php?id=<?= $data['id'] ?>'" value="回應">
                                <input type="button" class="btn btn-light edit" onclick="location.href='admineditcomment.php?id=<?= $data['id'] ?>'" value="修改">
                                <input type="button" class="btn btn-danger del" onclick="location.href='api.php?admindeletecomment=<?= $data['id'] ?>'" value="刪除">
                            </div>
                        </div>
                    <?php
                }
            ?>
        </div>

        <script src="init.js"></script> 
    </body>
</html>