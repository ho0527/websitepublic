<?php
    date_default_timezone_set("Asia/Taipei");
    $time=date("Y-m-d H:i:s");
    session_start();

    function query($query,$data=[]){
        $db=new PDO("mysql:host=localhost;dbname=test4;charset=utf8","root","");
        $p=$db->PREPARE($query);
        $p->EXECUTE($data);
        return $p->fetchAll();
    }

    function padstart($l,$data){
        return str_pad($data,$l,"0",STR_PAD_LEFT);
    }
?>