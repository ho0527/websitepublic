<?php
    $db=new PDO("mysql:host=localhost;dbname=53regionals1;charset=utf8","admin","1234");
    date_default_timezone_set("Asia/Taipei");
    $time=date("Y-m-d H:i:s");
    session_start();

    function query($db,$query,$data=[]){
        $p=$db->prepare($query);
        $p->execute($data);
        return $p->fetchAll();
    }
?>