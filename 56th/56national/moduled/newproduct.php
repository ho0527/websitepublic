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
            <input type="button" onclick="location.href='../products'" value="back">
        </div>

        <div class="main littlemain">
            <form method="POST">
                <label>
                    <p>company<p>
                    <div>
                        <select name="companyid">
                            <?php
                                $companyrow=query($db,"SELECT*FROM `company` WHERE `deactivatetime` IS NULL");
                                if(0==count($companyrow)){
                                    ?><script>alert("pls create company first");location.href="../company"</script><?php
                                }
                                for($i=0;$i<count($companyrow);$i=$i+1){
                                    ?><option value="<?= $companyrow[$i]['id'] ?>"><?= $companyrow[$i]['name'] ?></option><?php
                                }
                            ?>
                        </select>
                    </div>
                </label>
                <label>
                    <p>english name<p>
                    <div><input type="text" name="enname"></div>
                </label>
                <label>
                    <p>french name<p>
                    <div><input type="text" name="frname"></div>
                </label>
                <label>
                    <p>gtin<p>
                    <div><input type="text" name="gtin"></div>
                </label>
                <label>
                    <p>english description<p>
                    <div><textarea name="endescription"></textarea></div>
                </label>
                <label>
                    <p>french description<p>
                    <div><textarea name="frdescription"></textarea></div>
                </label>
                <label>
                    <p>brand name<p>
                    <div><input type="text" name="brandname"></div>
                </label>
                <label>
                    <p>country of origin<p>
                    <div><input type="text" name="country"></div>
                </label>
                <label>
                    <p>gross weight<p>
                    <div><input type="number" name="grossweight" step="0.01"></div>
                </label>
                <label>
                    <p>net content weight<p>
                    <div><input type="number" name="contentweight" step="0.01"></div>
                </label>
                <label>
                    <p>weight unit<p>
                    <div><input type="text" name="weightunit"></div>
                </label>
                <div class="textcenter">
                    <input type="button" onclick="location.href='../products'" value="back">
                    <input type="submit" name="submit" value="submit">
                </div>
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                $companyid=$_POST["companyid"];
                $enname=$_POST["enname"];
                $frname=$_POST["frname"];
                $gtin=$_POST["gtin"];
                $endescription=$_POST["endescription"];
                $frdescription=$_POST["frdescription"];
                $brandname=$_POST["brandname"];
                $country=$_POST["country"];
                $grossweight=$_POST["grossweight"];
                $contentweight=$_POST["contentweight"];
                $weightunit=$_POST["weightunit"];
                if(preg_match("/^[0-9]{13,14}$/",$gtin)&&!query($db,"SELECT*FROM `product` WHERE `gtin`=?",[$gtin])){
                    query($db,"INSERT INTO `product`(`companyid`,`image`,`enname`,`frname`,`gtin`,`endescription`,`frdescription`,`brandname`,`country`,`grossweight`,`contentweight`,`weightunit`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",[$companyid,"/20250614/05_module_d/placeholder.jpg",$enname,$frname,$gtin,$endescription,$frdescription,$brandname,$country,$grossweight,$contentweight,$weightunit])
                    
                    ?><script>alert("success:)");location.href="../products"</script><?php
                }else{
                    ?><script>alert("gtin error");location.href=""</script><?php
                }
            }
        ?>
    </body>
</html>