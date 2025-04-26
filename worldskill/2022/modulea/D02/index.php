<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Document</title>
    </head>
    <body>
        <form>
            <input type="text" name="factor">
            <input type="submit" value="submit">
        </form>
        <?php
            if(isset($_GET["factor"])){
                $array=range(1,40);
                $factor=(int)$_GET["factor"];

                $array=array_map(function($value)use($factor){
                    if($value%$factor==0){ return $value." is a factor of ".$factor."**"."<br>"; }
                    else{ return $value."<br>"; }
                },$array);

                echo("Modify ");
                print_r($array);
            }else{
                echo("pls enter factor");
            }
        ?>
    </body>
</html>