<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>title</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            if(isset($_POST["submit"])){
                $text=$_POST["input"];
                $xml=simplexml_load_string($text);
                $json=json_encode($xml);
            }
        ?>
        <form method="POST">
            <div class="grid">
                <div class="left">
                    <textarea class="textarea" id="input" name="input" cols="30" rows="10"></textarea>
                    <input type="submit" class="button" name="submit" value="conver">
                </div>
                <div class="right">
                    <div class="show" id="show"><?= @$json ?></div>
                </div>
            </div>
        </form>
    </body>
</html>