<?php
    $lang=$_GET["lang"]??"en";
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="/20250614/05_module_d/index.css">
    </head>
    <body>
        <?php
            include("link.php");
            $gtin=$_GET["gtin"]??0;
            $row=query($db,"SELECT*FROM `product` WHERE `gtin`=? AND `hidetime` IS NULL",[$gtin]);
            if($row){
                $row=$row[0];
                $compnayrow=query($db,"SELECT*FROM `company` WHERE `id`=?",[$row["companyid"]])[0];
                ?>
                <div>
                    <input type="button" onclick="location.href='?lang=en'" value="EN">
                    <input type="button" onclick="location.href='?lang=fr'" value="FR">
                </div>

                <div class="main littlemain">
                    <div class="textcenter margin">
                        <?= $compnayrow["name"] ?>
                    </div>
                    <div class="textcenter margin">
                        <img src="<?= $row['image'] ?>" style="width:250px">
                    </div>
                    <div class="textcenter margin width-100per">
                        <div><?= $row[$lang."name"] ?></div>
                    </div>
                    <div class="textcenter margin width-100per">
                        <div><?= $row["gtin"] ?></div>
                    </div>
                    <div class="textcenter margin width-100per">
                        <textarea style="width: 90%;height:100px" disabled><?= $row[$lang."description"] ?></textarea>
                    </div>
                    <div class="textcenter margin width-100per">
                        <p>weight: <?= $row["grossweight"] ?><?= $row["weightunit"] ?></p>
                        <p>net content weight: <?= $row["contentweight"] ?><?= $row["weightunit"] ?></p>
                    </div>
                </div>
                <?php
            }else{
                http_response_code(404);
                echo("404 NOT FOUND");
                echo("<input type='button' onclick=\"location.href='../products'\" value='back to products'>");
            }
        ?>
    </body>
</html>