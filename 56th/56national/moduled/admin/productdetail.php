<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="/20250614/05_module_d/index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["signin"])){ http_response_code(401);echo("<input type='button' onclick=\"location.href='./login'\" value='back to login'>"); }
            $id=$_GET["id"]??0;
            $row=query($db,"SELECT*FROM `product` WHERE `gtin`=? AND `hidetime` IS NULL",[$id]);
            if($row){
                $row=$row[0]
                ?>
                <div>
                    <input type="button" onclick="location.href='../products'" value="back">
                </div>

                <div class="main">
                    <div class="textcenter">
                        <input type="button" onclick="location.href='../editproduct?id=<?= $row['id'] ?>'" value="edit">
                        <?php
                            if($row["hidetime"]==null){
                                ?><input type="button" onclick="location.href='../api.php?key=hideproduct&id=<?= $row['id'] ?>'" value="hide"><?php
                            }else{
                                ?><input type="button" onclick="location.href='../api.php?key=deleteproduct&id=<?= $row['id'] ?>'" value="delete"><?php
                            }
                        ?>
                    </div>
                    <div class="textcenter">
                        <img src="<?= $row['image'] ?>" style="width:250px">
                    </div>
                    <div class="flex margin">
                        <div class="textcenter width-100per">
                            <p>english name</p>
                            <div><?= $row['enname'] ?></div>
                        </div>
                        <div class="textcenter width-100per">
                            <p>french name</p>
                            <div><?= $row['frname'] ?></div>
                        </div>
                        <div class="textcenter width-100per">
                            <p>gtin</p>
                            <div><?= $row['gtin'] ?></div>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="textcenter width-100per">
                            <p>english description</p>
                            <textarea style="width: 90%;height:100px" disabled><?= $row['endescription'] ?></textarea>
                        </div>
                        <div class="textcenter width-100per">
                            <p>french description</p>
                            <textarea style="width: 90%;height:100px" disabled><?= $row['frdescription'] ?></textarea>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="textcenter width-100per">
                            <p>brand name</p>
                            <div><?= $row['brandname'] ?></div>
                        </div>
                        <div class="textcenter width-100per">
                            <p>country of orignin</p>
                            <div><?= $row['country'] ?></div>
                        </div>
                        <div class="textcenter width-100per">
                            <p>gross weight</p>
                            <div><?= $row['grossweight'] ?></div>
                        </div>
                        <div class="textcenter width-100per">
                            <p>net content weight</p>
                            <div><?= $row['contentweight'] ?></div>
                        </div>
                        <div class="textcenter width-100per">
                            <p>weight unit</p>
                            <div><?= $row['weightunit'] ?></div>
                        </div>
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