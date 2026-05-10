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
            $row=query($db,"SELECT*FROM `company` WHERE `id`=? AND `deactivatetime` IS NULL",[$id]);
            if($row){
                $row=$row[0]
                ?>
                <div>
                    <input type="button" onclick="location.href='./company'" value="back">
                </div>

                <div class="main">
                    <form method="POST">
                        <div class="flex margin">
                            <div class="textcenter width-100per">
                                <p>company name</p>
                                <div><?= $row['name'] ?></div>
                            </div>
                            <div class="textcenter width-100per">
                                <p>company address</p>
                                <div><?= $row['address'] ?></div>
                            </div>
                            <div class="textcenter width-100per">
                                <p>company telephone number</p>
                                <div><?= $row['phone'] ?></div>
                            </div>
                            <div class="textcenter width-100per">
                                <p>company email address</p>
                                <div><?= $row['email'] ?></div>
                            </div>
                        </div>
                        <div class="textcenter margin">
                            owner's
                            <div class="flex">
                                <div class="textcenter width-100per">
                                    <p>name</p>
                                    <div><?= $row['ownername'] ?></div>
                                </div>
                                <div class="textcenter width-100per">
                                    <p>mobile number</p>
                                    <div><?= $row['ownerphone'] ?></div>
                                </div>
                                <div class="textcenter width-100per">
                                    <p>email address</p>
                                    <div><?= $row['owneremail'] ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="textcenter margin">
                            contact's
                            <div class="flex">
                                <div class="textcenter width-100per">
                                    <p>name</p>
                                    <div><?= $row['contactname'] ?></div>
                                </div>
                                <div class="textcenter width-100per">
                                    <p>mobile number</p>
                                    <div><?= $row['contactphone'] ?></div>
                                </div>
                                <div class="textcenter width-100per">
                                    <p>email address</p>
                                    <div><?= $row['contactemail'] ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="product">
                            <table>
                                <tr>
                                    <th class="littletd">#</th>
                                    <th>image</th>
                                    <th>name</th>
                                    <th>gtin</th>
                                    <th>function</th>
                                </tr>
                                <?php
                                    $row=query($db,"SELECT*FROM `product` WHERE `companyid`=?",[$row["id"]]);
                                    for($i=0;$i<count($row);$i=$i+1){
                                        ?>
                                        <tr>
                                            <td class="littletd"><?= $i+1 ?></td>
                                            <td>
                                                <img src="<?= $row[$i]["image"] ?>" style="width: 250px;">
                                            </td>
                                            <td><?= $row[$i]["enname"] ?></td>
                                            <td><?= $row[$i]["gtin"] ?></td>
                                            <td class="textcenter">
                                                <input type="button" onclick="location.href='./products/<?= $row[$i]['gtin'] ?>'" value="detail">
                                                <?php
                                                    if($row[$i]["hidetime"]==null){
                                                        ?><input type="button" onclick="location.href='./api.php?key=hideproduct&id=<?= $row[$i]['id'] ?>'" value="hide"><?php
                                                    }else{
                                                        ?><input type="button" onclick="location.href='./api.php?key=deleteproduct&id=<?= $row[$i]['id'] ?>'" value="delete"><?php
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                ?>
                            </table>
                        </div>
                    </form>
                </div>
                <?php
            }else{
                http_response_code(404);
                echo("404 NOT FOUND");
                echo("<input type='button' onclick=\"location.href='./company'\" value='back to company'>");
            }
        ?>
    </body>
</html>