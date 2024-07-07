<?php
    include("link.php");
    $site=$_POST["site"];
    $travel=$_POST["travel"];
    $stop=$_POST["stop"];
    $pdo->query("INSERT INTO `site`( `site`, `travel`, `stop`) VALUES ('$site','$travel','$stop')");
    ?><script>location="./user1.php"</script><?php
?>