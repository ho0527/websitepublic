<?php
include("link.php");
if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    $pdo->query("DELETE FROM `bus` WHERE `id`=$id");
    ?><script>location="./user.php"</script><?php
}
