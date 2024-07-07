<?php
    $db=new PDO("mysql:host=localhost;dbname=web00;charset=utf8","root","");
    session_start();
    date_default_timezone_set("Asia/Taipei");
    $time=date("Y-m-d H:i:s");

    function query($db,$query,$data=[]){
        $p=$db->PREPARE($query);
        $p->EXECUTE($data);
        return $p->fetchAll();
    }
?>