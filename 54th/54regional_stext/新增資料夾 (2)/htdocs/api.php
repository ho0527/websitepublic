<?php
    include("link.php");

    if(isset($_GET["signout"])){
        session_unset();
        ?><script>alert("登出成功");location.href="signin.php"</script><?php
    }
?>