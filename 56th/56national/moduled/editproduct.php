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
            $row=query($db,"SELECT*FROM `product` WHERE `id`=? AND `hidetime` IS NULL",[$id]);
            if($row){
                $row=$row[0]
                ?>
                <div>
                    <input type="button" onclick="location.href='./company'" value="back">
                </div>

                <div class="main littlemain">
                    <form method="POST">
                        <input type="file" name="image" id="image" accept="image/*">
                        <div class="textcenter">
                            <input type="button" onclick="document.getElementById('imagebase64').value='/20250614/05_module_d/placeholder.jpg'" value="clear image">
                            <input type="submit" name="submit" value="submit">
                        </div>
                        <input type="hidden" name="imagebase64" id="imagebase64" value="<?= $row['image'] ?>">
                        <input type="hidden" name="id" value="<?= $id ?>">
                    </form>
                </div>

                <?php
                    if(isset($_POST["submit"])){
                        $imagebase64=$_POST["imagebase64"];
                        query($db,
                            "UPDATE `product` SET `image`=? WHERE `id`=?",
                            [$imagebase64,$id]
                        )
                        
                        ?><script>alert("success:)");location.href="./products"</script><?php
                    }
                ?>
                <?php
            }else{
                http_response_code(404);
                echo("404 NOT FOUND");
                echo("<input type='button' onclick=\"location.href='./products'\" value='back to company'>");
            }
        ?>

        <script>
            document.getElementById("image").onchange=function(){
                let file=this.files[0]
                console.log(file)
                let filereader=new FileReader()
                filereader.onload=function(){
                    document.getElementById("imagebase64").value=filereader.result
                }
                filereader.readAsDataURL(file)
            }
        </script>
    </body>
</html>