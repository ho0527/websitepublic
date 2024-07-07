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
            if(!isset($_SESSION["signin"])){
                header("location: signin.php");
            }
        ?>
        <div class="nav" id="adminnav">
            <div class="title">網站管理 - 留言管理</div>
        </div>
        <div class="nav2">
            <div class="btn-group">
                <a href="admincomment.php" class="btn btn-outline-light active">留言管理</a>
                <a href="adminbookroom.php" class="btn btn-outline-light">訂房管理</a>
                <a href="adminorderfood.php" class="btn btn-outline-light">訂餐管理</a>
            </div>
            <form class="margin-0px_10px">
                <input type="text" class="width-init" name="keyword" placeholder="關鍵字">
                <select name="orderby">
                    <option value="createtime">發表日期</option>
                    <option value="name">姓名</option>
                    <option value="email">email</option>
                    <option value="phone">連絡電話</option>
                    <option value="content">留言內容</option>
                </select>
                <select name="ordertype">
                    <option value="DESC">降冪</option>
                    <option value="ASC">升冪</option>
                </select>
                <input type="submit" name="submit" class="btn btn-warning" value="送出">
            </form>
        </div>

        <div class="commentmain">
            <?php
                $keyword="";
                $orderby="createtime";
                $ordertype="DESC";
                if(isset($_GET["keyword"])){
                    $keyword=$_GET["keyword"];
                }
                if(isset($_GET["orderby"])){
                    $orderby=$_GET["orderby"];
                }
                if(isset($_GET["ordertype"])){
                    $ordertype=$_GET["ordertype"];
                }
                $row=query("SELECT*FROM `comment` WHERE (`name` LIKE ?) OR (`email` LIKE ?) OR (`phone` LIKE ?) OR (`content` LIKE ?) ORDER BY `pin` DESC,`$orderby` $ordertype",["%".$keyword."%","%".$keyword."%","%".$keyword."%","%".$keyword."%"]);
                for($i=0;$i<count($row);$i=$i+1){
                    $data=$row[$i];
                    ?>
                        <div class="commentdiv <?= $row[$i]["pin"] ?>">
                            <div class="commentdivname"><?= $data["name"] ?></div>
                            <div class="commentdivmid">
                                <div class="commentdivcontent"><?= (($data["deletetime"]=="")?("<img src='".$data["image"]."' class='image'>".$data["content"]):("")); ?></div>
                                <div class="commentdivreply"><?= (($data["deletetime"]=="")?("管理員回應: ".$data["reply"]):("")); ?></div>
                                <div class="commentdivtime"><?= ("發表於 ".$data["createtime"]).(($data["deletetime"]=="")?(($data["updatetime"]=="")?(""):("，修改於 ".$data["updatetime"])):("，刪除於 ".$data["deletetime"])); ?></div>
                                <div class="commentdivconnent"><?= (($data["deletetime"]=="")?((($data["emailshow"]=="")?("email: 未顯示"):("email: ".$data["email"]))."，".(($data["phoneshow"]=="")?("聯絡電話: 未顯示"):("聯絡電話: ".$data["phone"]))):("")); ?></div>
                            </div>
                            <div class="commentdivfunction">
                                <input type='button' class='btn btn-light' onclick="location.href='admineditcomment.php?id=<?= $data['id'] ?>'" value='修改'>
                                <input type='button' class='btn btn-light' onclick="location.href='adminreplycomment.php?id=<?= $data['id'] ?>'" value='回應'>
                                <input type='button' class='btn btn-light' onclick="location.href='api.php?pincomment=<?= $data['id'] ?>'" value='<?= $data["pin"]=="pin"?"解除":"" ?>置頂'>
                                <input type='button' class='btn btn-danger' onclick="if(confirm('confirm?')) location.href='api.php?admindeletecomment=<?= $data['id'] ?>'" value='刪除'>
                            </div>
                        </div>
                    <?php
                }
            ?>
        </div>


        <script src="init.js"></script>
    </body>
</html>