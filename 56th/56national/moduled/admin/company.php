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
            <input type="button" onclick="location.href='./newcompany'" value="new company">
            <input type="button" onclick="location.href='./deactivatecompany'" value="deactivate company">
            <input type="button" onclick="location.href='./products'" value="products">
        </div>

        <div class="main">
            <table>
                <tr>
                    <th class="littletd">#</th>
                    <th>name</th>
                    <th>function</th>
                </tr>
                <?php
                    $row=query($db,"SELECT*FROM `company`");
                    for($i=0;$i<count($row);$i=$i+1){
                        ?>
                        <tr>
                            <td class="littletd"><?= $i+1 ?></td>
                            <td class="textcenter"><?= $row[$i]["name"] ?></td>
                            <td class="textcenter">
                                <input type="button" onclick="location.href='./comapnydetail?id=<?= $row[$i]['id'] ?>'" value="detail">
                                <input type="button" onclick="location.href='./editcompany?id=<?= $row[$i]['id'] ?>'" value="edit">
                                <?php
                                    if($row[$i]["deactivatetime"]==null){
                                        ?><input type="button" onclick="location.href='./api.php?key=deactivatecompany&id=<?= $row[$i]['id'] ?>'" value="deactivate"><?php
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