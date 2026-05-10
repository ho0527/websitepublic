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

                <div class="main littlemain">
                    <form method="POST">
                        <label>
                            <p>company name<p>
                            <div><input type="text" name="name" value="<?= $row['name'] ?>"></div>
                        </label>
                        <label>
                            <p>company address<p>
                            <div><input type="text" name="address" value="<?= $row['address'] ?>"></div>
                        </label>
                        <label>
                            <p>company telephone number<p>
                            <div><input type="text" name="phone" value="<?= $row['phone'] ?>"></div>
                        </label>
                        <label>
                            <p>company email address<p>
                            <div><input type="text" name="email" value="<?= $row['email'] ?>"></div>
                        </label>
                        <label>
                            <p>owner's name<p>
                            <div><input type="text" name="ownername" value="<?= $row['ownername'] ?>"></div>
                        </label>
                        <label>
                            <p>owner's mobile number<p>
                            <div><input type="text" name="ownerphone" value="<?= $row['ownerphone'] ?>"></div>
                        </label>
                        <label>
                            <p>owner's email address<p>
                            <div><input type="text" name="owneremail" value="<?= $row['owneremail'] ?>"></div>
                        </label>
                        <label>
                            <p>contact's name<p>
                            <div><input type="text" name="contactname" value="<?= $row['contactname'] ?>"></div>
                        </label>
                        <label>
                            <p>contact's mobile number<p>
                            <div><input type="text" name="contactphone" value="<?= $row['contactphone'] ?>"></div>
                        </label>
                        <label>
                            <p>contact's email address<p>
                            <div><input type="text" name="contactemail" value="<?= $row['contactemail'] ?>"></div>
                        </label>
                        <div class="textcenter">
                            <input type="button" onclick="location.href='./company'" value="back">
                            <input type="submit" name="submit" value="submit">
                        </div>
                        <input type="hidden" name="id" value="<?= $id ?>">
                    </form>
                </div>

                <?php
                    if(isset($_POST["submit"])){
                        $id=$_POST["id"];
                        $name=$_POST["name"];
                        $address=$_POST["address"];
                        $phone=$_POST["phone"];
                        $email=$_POST["email"];
                        $ownername=$_POST["ownername"];
                        $ownerphone=$_POST["ownerphone"];
                        $owneremail=$_POST["owneremail"];
                        $contactname=$_POST["contactname"];
                        $contactphone=$_POST["contactphone"];
                        $contactemail=$_POST["contactemail"];
                        query($db,
                            "UPDATE `company` SET `name`=?,`address`=?,`phone`=?,`email`=?,`ownername`=?,`ownerphone`=?,`owneremail`=?,`contactname`=?,`contactphone`=?,`contactemail`=? WHERE `id`=?",
                            [$name,$address,$phone,$email,$ownername,$ownerphone,$owneremail,$contactname,$contactphone,$contactemail,$id]
                        )
                        
                        ?><script>alert("success:)");location.href="./company"</script><?php
                    }
                ?>
                <?php
            }else{
                http_response_code(404);
                echo("404 NOT FOUND");
                echo("<input type='button' onclick=\"location.href='./company'\" value='back to company'>");
            }
        ?>
    </body>
</html>