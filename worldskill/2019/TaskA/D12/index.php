<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>xml to json</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
        if (isset($_POST["submit"])){
            $xml=simplexml_load_string($_POST["input"]);
            $json=json_encode($xml,JSON_PRETTY_PRINT); 
        }
        ?>
        <form method="POST">
            <div class="grid">
                <div class="left">
                    <textarea class="textarea" id="input" name="input" cols="30" rows="10"></textarea>
                    <input type="submit" class="button" name="submit" value="conver">
                </div>
                <div class="right">
                    <textarea class="textarea2" class="show" id="show"><?= @$json ?></textarea>
                </div>
            </div>
        </form>
    </body>
</html>