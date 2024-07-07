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
        <div class="nav" id="nav">
            <div class="title">訪客留言版</div>
        </div>
        <div class="nav2">
            <div class="title2">訪客留言列表</div>
            <input type="button" class="btn btn-warning" onclick="location.href='newc.php'" value="aaa">
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
                        <div class="commentdivfunction center"><?= ($row[$i]["deletetime"]=="")?"
                            <input type='text' class='input' id='".$row[$i][0]."' placeholder='留言序號'>
                            <input type='button' class='btn btn-light editbutton' data-id='".$row[$i][0]."' data-no='".$row[$i]['no']."' value='修改'>
                            <input type='button' class='btn btn-danger deletebutton' data-id='".$row[$i][0]."' data-no='".$row[$i]['no']."' value='刪除'>
                        ":"已刪除" ?></div>
                    </div>
                    <?php
                }
            ?>   
        </div>

        <script src="init.js"></script>
        <script src="comment.js"></script>
    </body>
</html>