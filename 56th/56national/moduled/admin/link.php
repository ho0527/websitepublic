<?php
    $db=new PDO("mysql:host=localhost;dbname=05_module_d;charset=utf8","web05","74fmdz");
    date_default_timezone_set("Asia/Taipei");
    $time=date("Y-m-d H:i:s");
    session_start();

    function query($db,$query,$data=[]){
        $row=$db->prepare($query);
        $row->execute($data);
        return $row->fetchAll();
    }
?>