<?php
    $db=new PDO("mysql:host=localhost;dbname=52jnational;charset=utf8","admin","1234");
    date_default_timezone_set("Asia/Taipei");
    $date=date("Y-m-d H:i:s");
    session_start();

    function query($db,$query,$data=[]){
       $prepare=$db->prepare($query);
       $prepare->execute($data);
       return $prepare->fetchAll();
    }

    if(isset($_GET["logout"])){
        if(isset($_SESSION["data"])){
            ?><script>alert("登出成功!");location.href="login.php"</script><?php
            session_unset();
        }else{
            ?><script>alert("請先登入!");location.href="login.php"</script><?php
        }
    }
?>