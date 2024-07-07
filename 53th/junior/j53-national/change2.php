<?php
    include("link.php");
    $travel = $_POST["travel"];
    $stop = $_POST["stop"];
    $id = $_POST["id"];
    $pdo->query("UPDATE `site` SET `travel`='$travel',`stop`='$stop' WHERE `id`=$id");
    ?><script>location="./user1.php"</script><?php
?>