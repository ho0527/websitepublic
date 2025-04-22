<?php
    $db=new PDO("mysql:host=localhost;dbname=worldskill2019modulec;charset=utf8","root","");
    date_default_timezone_set("Asia/Taipei");
    $time=date("Y-m-d H:i:s");
    session_start();

    function query($db,$query,$data=[]){
        $row=$db->prepare($query);
        $row->execute($data);
        return $row->fetchAll();
    }
?>