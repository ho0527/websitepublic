<?php
    session_start();
    include("link.php");
    $id=$_GET["id"];
    $name=$_GET["name"];
    $snum=$_GET["snum"];
    $RowOfMsgnum=$db->query("SELECT*FROM `message` WHERE `id`='$id'")->fetch();
    if($RowOfMsgnum[9]==$snum){
        if($name=="edit"){
            $_SESSION["id-for-edit"]=$id;
            $_SESSION['passcode']="pass";
            ?>
                <script>
                    alert("序號正確");location.href="chat.php";
                </script>
            <?php
            //edit的內容
        }else{
            $db->query("UPDATE `message` SET `deltime`='$date' WHERE `id`='$id'");
            ?>
                <script>
                    alert("刪除成功!");location.href="chat.php";
                </script>
            <?php
            //del的內容
        }
    }else{
        // print_r($RowOfMsgnum)
        ?>
        <script>
            alert("序號錯誤");location.href="chat.php";
        </script><?php
    }
?>