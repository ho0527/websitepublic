<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Shanghai Battle!</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <img src="banner.png" class="banner">
        <div class="navigationbar">
            <img src="logo.png" class="logo">
            <div class="navigationbarbuttondiv">
                <input type="button" class="navigationbarbutton" onclick="location.href='index.php'" value="玩家留言">
                <input type="button" class="navigationbarbutton" onclick="location.href='post.php'" value="玩家參賽">
                <input type="button" class="navigationbarbutton selectbutton" onclick="location.href='login.php'" value="網站管理">
                <input type="button" class="navigationbarbutton" onclick="location.href='link.php?logout='" value="登出">
            </div>
        </div>
        <?php
            include("link.php");
            if(isset($_SESSION["data"])){
                ?>
                <div class="loginhead">
                    <div class="center">
                        <input type="button" class="loginbutton button" onclick="location.href='login.php'" value="留言管理">
                        <input type="button" class="loginbutton button selectbutton" onclick="location.href='comp.php'" value="賽制管理">
                    </div>
                </div>
                <div class="compdiv">
                    <form>
                        <table class="compmaintable">
                            <?php
                            $comp=query($db,"SELECT*FROM `comp`");
                            $maxid=query($db,"SELECT MAX(`id`) FROM `comp`")[0][0];
                            $countcomp=count($comp);
                            $maxnum=round($countcomp/2);
                            for($i=1;$i<=$maxnum;$i=$i+1){
                                $team=[];
                                for($j=1;$j<=$maxid;$j=$j+1){
                                    $temporarystorage=query($db,"SELECT*FROM `comp` WHERE `id`='$j' AND `team`='$i'");
                                    if($temporarystorage){
                                        $team[]=$temporarystorage[0];
                                    }
                                }
                                if(count($team)==2&&$team[0]!=""){
                                    ?>
                                    <tr>
                                        <td class="compplayerhead" name="playerhead<?= $team[0][0] ?>" rowspan="2"><img src="<?= $team[0][4] ?>" width="100px"></td>
                                        <td class="compusername" name="username<?= $team[0][0] ?>" rowspan="2"><?= $team[0][1] ?></td>
                                        <td class="compemail" name="email<?= $team[0][0] ?>"><?= $team[0][2] ?></td>
                                        <td class="vs" rowspan="2">VS</td>
                                        <td class="compplayerhead" name="playerhead<?= $team[1][0] ?>" rowspan="2"><img src="<?= $team[1][4] ?>" width="100px"></td>
                                        <td class="compusername" name="username<?= $team[1] ?>" rowspan="2"><?= $team[1][1] ?></td>
                                        <td class="compemail" name="email<?= $team[1] ?>"><?= $team[1][2] ?></td>
                                        <td class="disabled" rowspan="2"><button name="cancel" value="<?= $team[0][0] ?> <?= $team[1][0] ?>">取消配對</button></td>
                                    </tr>
                                    <tr>
                                        <td class="compphone" name="phone<?= $team[0][0] ?>"><?= $team[0][3] ?></td>
                                        <td class="compphone" name="phone<?= $team[1] ?>"><?= $team[1][3] ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            $noteamrow=query($db,"SELECT*FROM `comp` WHERE `team`=''");
                            for($i=0;$i<count($noteamrow);$i=$i+1){
                                ?>
                                <tr>
                                    <td class="compplayerhead" name="playerhead<?= $noteamrow[$i][0] ?>" rowspan="2"><img src="<?= $noteamrow[$i][4] ?>" width="100px"></td>
                                    <td class="compusername" name="username<?= $noteamrow[$i][0] ?>" rowspan="2"><?= $noteamrow[$i][1] ?></td>
                                    <td class="compemail" name="email<?= $noteamrow[$i][0] ?>"><?= $noteamrow[$i][2] ?></td>
                                    <td class="vs" rowspan="2">配對中</td>
                                </tr>
                                <tr>
                                    <td class="compphone" name="phone<?= $noteamrow[$i][0] ?>"><?= $noteamrow[$i][3] ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                        <input type="submit" class="random" name="submit" value="亂數配對">
                    </form>
                </div>
                <?php
                if(isset($_GET["submit"])){
                    $row=query($db,"SELECT*FROM `comp`")[0];
                    $ingame=query($db,"SELECT*FROM `comp` WHERE `team`!=''")[0];
                    $notingame=query($db,"SELECT*FROM `comp` WHERE `team`=''")[0];
                    $count=(int)(count($row)/2);
                    $range=range(1,$count);
                    for($i=0;$i<$count;$i=$i+1){
                        for($j=0;$j<count($ingame);$j=$j+1){
                            if($range[$i]==$ingame[$j][5]){
                                unset($range[$i]);
                                break;
                            }
                        }
                    }
                    $range=array_values($range);
                    $notingameid=[];
                    for($i=0;$i<count($notingame);$i=$i+1){
                        $notingameid[]=$notingame[$i][0];
                    }
                    if(count($notingameid)>1){
                        for($i=0;$i<count($range);$i=$i+1){
                            $rand=array_rand($notingameid,2);
                            $team=$range[$i];
                            $id1=$notingameid[$rand[0]];
                            $id2=$notingameid[$rand[1]];
                            query($db,"UPDATE `comp` SET `team`='$team' WHERE `id`='$id1'or`id`='$id2'");
                            unset($notingameid[$rand[0]]);
                            unset($notingameid[$rand[1]]);
                            $notingameid=array_values($notingameid);
                        }
                    }
                    ?><script>alert("亂數成功!");location.href="comp.php"</script><?php
                }
            }else{
                ?><script>alert("請先登入!");location.href="login.php"</script><?php
            }
            if(isset($_GET["cancel"])){
                $arr=explode(" ",$_GET["cancel"]);
                for($i=0;$i<2;$i=$i+1){
                    $temp=$arr[$i];
                    query($db,"UPDATE `comp` SET `team`='' WHERE `id`='$temp'");
                }
                ?><script>alert("取消配對成功!");location.href="comp.php"</script><?php
            }
        ?>
        <script src="index.js"></script>
    </body>
</html>