<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <?php
            $title=$_POST["title"];
            $description=$_POST["description"];
            ?><h1><?php echo("$title"); ?></h1><?php
            ?><p><?php echo("$description"); ?></p><?php

            header("Content-Type: application/vnd.ms-word");
            header("Content-Disposition: attachment; filename=".$title.".doc");
        ?>
    </body>
</html>