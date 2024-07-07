<?php
    $db=new PDO("mysql:host=localhost;dbname=test1;charset=utf8","root","");
    date_default_timezone_set("Asia/Taipei");
    $time=date("Y-m-d H:i:s");
    session_start();

    function query($db,$query,$data=[]){
        $p=$db->PREPARE($query);
        $p->EXECUTE($data);
        return $p->fetchAll();
    }
?>