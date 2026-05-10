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
            if(isset($_SESSION["signin"])){ header("location: ./company"); }
        ?>

        <form method="POST">
            password
            <input type="text" name="password">
            <input type="submit" value="submit">
        </form>

        <?php
            if(isset($_POST["password"])){
                if($_POST["password"]=="admin"){
                    $_SESSION["signin"]=true;
                    ?><script>alert("login success:)");location.href="./company"</script><?php
                }else{
                    ?><script>alert("login failed:(");location.href="./login"</script><?php
                }
            }
        ?>
    </body>
</html>