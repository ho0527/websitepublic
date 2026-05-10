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
        ?>

        <div>
            <input type="button" onclick="location.href='./products/new'" value="new product">
            <input type="button" onclick="location.href='./company'" value="company">
        </div>

        <div class="main">
            <table>
                <tr>
                    <th class="littletd">#</th>
                    <th>image</th>
                    <th>name</th>
                    <th>gtin</th>
                    <th>function</th>
                </tr>
                <?php
                    $row=query($db,"SELECT*FROM `product`");
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

        <?php
            if(isset($_POST["password"])){
                if($_POST["password"]=="admin"){
                    ?><script>alert("login success:)");location.href="./company"</script><?php
                }else{
                    ?><script>alert("login failed:(");location.href="login.php"</script><?php
                }
            }
        ?>
    </body>
</html>