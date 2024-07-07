<?php
    $db=new PDO("mysql:host=localhost;dbname=54r;charset=utf8","root","");
    session_start();
    date_default_timezone_set("Asia/Taipei");
    $time=date("Y-m-d H:i:s");

    function query($db,$query,$data=[]){
        $p=$db->PREPARE($query);
        $p->EXECUTE($data);
        return $p->fetchAll();
    }

    function padstart($data,$length){
        return sprintf("%0".$length."d",$data);
    }
?>