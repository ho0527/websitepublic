<?php
include("link.php");
if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    $pdo->query("DELETE FROM `site` WHERE `id`=$id");
    ?><script>location="./user1.php"</script><?php
}
