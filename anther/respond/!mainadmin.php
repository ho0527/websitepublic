<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>mainadmin</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <input type="button" class="button2" onclick="location.href='../../'" value="回到首頁">
        <div class="main">
            <form>
                <input type="search" name="search" placeholder="查詢">
                <button name="enter">送出</button>
            </form>
            <table class="maintable">
                <form>
                    <tr>
                        <td class="admintable">編號</td>
                        <td class="admintable">網址</td>
                        <td class="admintable">問題</td>
                        <td class="admintable">詳細敘述</td>
                        <td class="admintable">聯絡方式</td>
                        <td class="admintable">其他回復</td>
                        <td class="admintable">結果</td>
                        <td class="admintable">新增時間</td>
                        <td class="admintable">完成時間</td>
                        <td class="admintable">功能區</td>
                    </tr>
                    <?php
                        include("link.php");
                        if(isset($_GET["enter"])){
                            $_SESSION["type"]=$_GET["search"];
                            header("location:!mainadmin.php");
                        }
                        if(isset($_SESSION["type"])){
                            $type=$_SESSION["type"];
                            if($type==""){
                                unset($_SESSION["type"]);
                                header("location:!mainadmin.php");
                            }else{
                                $data=query($db,"SELECT*FROM `log` WHERE `id`LIKE'%$type%' or `question`LIKE'%$type%' or `context`LIKE'%$type%' or `email`LIKE'%$type%' or `anthor`LIKE'%$type%' or `finish`LIKE'%$type%' or `posttime`LIKE'%$type%' or `finishtime`LIKE'%$type%'");
                            }
                        }else{
                            $data=query($db,"SELECT*FROM `log`");
                        }
                        for($i=0;$i<count($data);$i=$i+1){
                            ?>
                            <tr>
                                <td class="admintable"><?php echo($data[$i][0]); ?></td>
                                <td class="admintable"><a href="<?php echo($data[$i][1]); ?>" class="a">link</a></td>
                                <td class="admintable"><?php echo($data[$i][2]); ?></td>
                                <td class="admintable"><?php echo($data[$i][3]); ?></td>
                                <td class="admintable"><?php echo($data[$i][4]); ?></td>
                                <td class="admintable"><?php echo($data[$i][5]); ?></td>
                                <td class="admintable"><?php echo($data[$i][6]); ?></td>
                                <td class="admintable"><?php echo($data[$i][7]); ?></td>
                                <td class="admintable"><?php echo($data[$i][8]); ?></td>
                                <td class="admintable">
                                    <input type="button" class="button3" onclick="location.href='?id=<?php echo($data[$i][0]) ?>&edit=cencel'" value="取消"><br>
                                    <input type="button" class="button3" onclick="location.href='?id=<?php echo($data[$i][0]) ?>&edit=updateing'" value="更新中"><br>
                                    <input type="button" class="button3" onclick="location.href='?id=<?php echo($data[$i][0]) ?>&edit=finish'" value="已完成"><br>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                </form>
            </table>
        </div>
        <?php
            if(isset($_GET["edit"])){
                $id=$_GET["id"];
                $edit=$_GET["edit"];
                query($db,"UPDATE `log` SET `finish`='$edit' WHERE `id`='$id'");
                if($_GET["edit"]=="finish"){
                    query($db,"UPDATE `log` SET `finishtime`='$time' WHERE `id`='$id'");
                }
                ?><script>location.href="!mainadmin.php"</script><?php
            }
        ?>
    </body>
</html>