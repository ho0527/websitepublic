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
        ?>
        <div class="nav" id="nav">
            <div class="title">訪客留言版</div>
        </div>
        <div class="nav2">
            <div class="title2">訪客留言列表</div>
            <a href="newcomment.php" class="btn btn-warning">新增留言</a>
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
                                <?= ($data["deletetime"]=="")?"
                                    <input type='text' id='".$data["id"]."' placeholder='序號'>
                                    <input type='button' class='btn btn-light edit' data-id='".$data["id"]."' data-no='".$data["no"]."' value='修改'>
                                    <input type='button' class='btn btn-danger del' data-id='".$data["id"]."' data-no='".$data["no"]."' value='刪除'>
                                ":"已刪除"?>
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