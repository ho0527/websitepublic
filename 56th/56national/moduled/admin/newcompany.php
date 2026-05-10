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
            <input type="button" onclick="location.href='./company'" value="back">
        </div>

        <div class="main littlemain">
            <form method="POST">
                <label>
                    <p>company name<p>
                    <div><input type="text" name="name"></div>
                </label>
                <label>
                    <p>company address<p>
                    <div><input type="text" name="address"></div>
                </label>
                <label>
                    <p>company telephone number<p>
                    <div><input type="text" name="phone"></div>
                </label>
                <label>
                    <p>company email address<p>
                    <div><input type="text" name="email"></div>
                </label>
                <label>
                    <p>owner's name<p>
                    <div><input type="text" name="ownername"></div>
                </label>
                <label>
                    <p>owner's mobile number<p>
                    <div><input type="text" name="ownerphone"></div>
                </label>
                <label>
                    <p>owner's email address<p>
                    <div><input type="text" name="owneremail"></div>
                </label>
                <label>
                    <p>contact's name<p>
                    <div><input type="text" name="contactname"></div>
                </label>
                <label>
                    <p>contact's mobile number<p>
                    <div><input type="text" name="contactphone"></div>
                </label>
                <label>
                    <p>contact's email address<p>
                    <div><input type="text" name="contactemail"></div>
                </label>
                <div class="textcenter">
                    <input type="button" onclick="location.href='./company'" value="back">
                    <input type="submit" name="submit" value="submit">
                </div>
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
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
                query($db,"INSERT INTO `company`(`name`,`address`,`phone`,`email`,`ownername`,`ownerphone`,`owneremail`,`contactname`,`contactphone`,`contactemail`) VALUES (?,?,?,?,?,?,?,?,?,?)",[$name,$address,$phone,$email,$ownername,$ownerphone,$owneremail,$contactname,$contactphone,$contactemail])
                
                ?><script>alert("success:)");location.href="./company"</script><?php
            }
        ?>
    </body>
</html>