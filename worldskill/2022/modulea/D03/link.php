<?php
    $db=new PDO("mysql:host=localhost;dbname=53nationala17;charset=utf8","root","");
    date_default_timezone_set("Asia/Taipei");
    $time=date("Y-m-d H:i:s");
    session_start();

    function query($db,$query,$data=[]){
        $prepare=$db->prepare($query);
        $prepare->execute($data);
        return $prepare->fetchAll();
    }
?>