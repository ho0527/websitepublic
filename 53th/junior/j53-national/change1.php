<?php
    include("link.php");
    $time = $_POST["time"];
    $id = $_POST["id"];
    $pdo->query("UPDATE `bus` SET `time`='$time' WHERE`id`=$id");
    ?><script>location="./user.php"</script><?php
?>