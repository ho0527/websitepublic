<?php
    $db=new PDO("mysql:host=localhost;dbname=51regional;charset=utf8","root","");
    date_default_timezone_set("Asia/Taipei");
    $time=date("Y-m-d H:i:s");
    session_start();

    function query($db,$query,$data=[]){
        $prepare=$db->prepare($query);
        $prepare->execute($data);
        return $prepare->fetchAll();
    }

    function removearraykey($array,$removeplace){
        if(($key=array_search($removeplace,$array))!=false) {
            unset($array[$key]);
        }
        return array_values($array);
    }

    function removearrayvalue($array,$removekey){
        $key=array_search($removekey,$array);
        // Remove the value
        if($key!=false){
            unset($array[$key]);
        }
        // Re-index the array
        $array=array_values($array);
        // Output the array
        print_r($array);
    }
?>