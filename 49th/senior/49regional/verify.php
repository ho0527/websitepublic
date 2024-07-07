<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>翻牌配對驗證模組</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            // if(!isset($_SESSION["data"])){ header("location:index.php"); }else{ if($_SESSION["data"]!="a0000"){ header("location:main.php"); } }
            $strarray=[];
            $randarray=[];
            while(count($strarray)<3){
                $rand=range("A","Z")[rand(0,25)];
                if($rand!="E"){
                    $strarray[]=$rand;
                    for($i=0;$i<(count($strarray)-1);$i=$i+1){
                        if($strarray[$i]==$rand){
                            array_pop($strarray);
                            break;
                        }
                    }
                }
            }
            $strarray[]="E";
            for($i=0;$i<4;$i=$i+1){
                $strarray[]=$strarray[$i];
            }
            while(count($randarray)<8){
                $rand=rand(0,7);
                $randarray[]=$rand;
                for($i=0;$i<(count($randarray)-1);$i=$i+1){
                    if($randarray[$i]==$rand){
                        array_pop($randarray);
                        break;
                    }
                }
            }
        ?>
        <div class="main">
            <h1>電子競技網站管理</h1><hr>
            <h2 class="mag">翻牌配對驗證模組</h2>
            <table class="verifytable mag">
                <tr>
                    <td class="verifytd" id="0"><?php echo($strarray[$randarray[0]]) ?></td>
                    <td class="verifytd" id="1"><?php echo($strarray[$randarray[1]]) ?></td>
                    <td class="verifytd" id="2"><?php echo($strarray[$randarray[2]]) ?></td>
                    <td class="verifytd" id="3"><?php echo($strarray[$randarray[3]]) ?></td>
                </tr>
                <tr>
                    <td class="verifytd" id="4"><?php echo($strarray[$randarray[4]]) ?></td>
                    <td class="verifytd" id="5"><?php echo($strarray[$randarray[5]]) ?></td>
                    <td class="verifytd" id="6"><?php echo($strarray[$randarray[6]]) ?></td>
                    <td class="verifytd" id="7"><?php echo($strarray[$randarray[7]]) ?></td>
                </tr>
            </table>
            <input type="button" class="button" onclick="location.href='api.php?logout='" value="登出">
            <input type="button" class="button" onclick="location.reload()" value="重整">
            <input type="button" class="button" id="open" onclick="showall()" value="全部翻牌">
        </div>
        <script src="verify.js"></script>
        <script src="logincheck.js"></script>
    </body>
</html>