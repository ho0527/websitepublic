<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="/20250614/05_module_d/index.css">
    </head>
    <body>
        <input type="button" onclick="location.href='./'" value="back">

        <form method="POST">
            <div class="main flex">
                <textarea style="width: 40%;height:400px" name="in" placeholder="in"></textarea>
                <div><input type="submit" name="check" value="check"></div>
                <div style="width: 40%;height:400px;overflow-y:auto;">
                    <?php
                        if(isset($_POST["in"])){
                            include("link.php");
                            $allsuccess=true;
                            $data=explode("\r\n",$_POST["in"]);
                            for($i=0;$i<count($data);$i=$i+1){
                                $row=query($db,"SELECT*FROM `product` WHERE `gtin`=? AND `hidetime` IS NULL",[$data[$i]]);
                                ?><p><?= $data[$i] ?> <?= $row?"Vaild":"Invalid" ?></p><?php
                                if(!$row){ $allsuccess=false; }
                            }

                            if($allsuccess){
                                ?>全部正確<img src="green-tick-2.png" alt=""><?php
                            }
                        }
                    ?>
                </div>
            </div>
        </form>
    </body>
</html>