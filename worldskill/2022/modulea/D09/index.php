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
                <textarea name="text" cols="30" rows="10"></textarea>
                <input type="submit" name="submit" value="submit">
            </form>
            <div class="output">
                <?php
                    if(isset($_POST["submit"])){
                        $text=$_POST["text"];
                        $text=explode(" ",$text);
                        $ans=[];
                        for($i=0;$i<count($text);$i=$i+1){
                            if($text[$i]%2==0){
                                $ans[]=$text[$i]/2;
                            }
                        }

                        sort($ans);
                        echo(implode(" ",$ans));
                    }
                ?>
            </div>
        </div>
    </body>
</html>