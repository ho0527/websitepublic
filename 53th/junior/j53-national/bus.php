<?php
    include("link.php");
    $car=$_POST["car"];
    $time=$_POST["time"];
    $pdo->query("INSERT INTO `bus`( `car`, `time`) VALUES ('$car','$time')");
    ?><script>location="./user.php"</script><?php
?>