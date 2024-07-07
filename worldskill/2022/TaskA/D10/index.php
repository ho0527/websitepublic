<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>php list of even number</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <div class="main">
            <form method="post">
            <input type="text" name="text">
                <input type="submit" name="submit" value="submit">
            </form>
            <div class="output">
                <?php
                    if(isset($_POST["submit"])){
                        $text=$_POST["text"];
                        echo(preg_replace('/[0-9]+/', '', $text));
                    }
                ?>
            </div>
        </div>
    </body>
</html>