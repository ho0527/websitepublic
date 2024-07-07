<?php
    $db=new PDO("mysql:host=localhost;dbname=53regional;charset=utf8","root","");
    date_default_timezone_set("Asia/Taipei");
    $time=date("Y-m-d H:i:s");
    session_start();

    function query($db,$query){
        return $db->query($query);
    }

    function fetch($result){
        return $result->fetch();
    }

    function fetchall($result){
        return $result->fetchAll();
    }

    function block($name){
        return preg_match("/([ ,\!,\@,\#,\$,\%,\^,\&,\*,\(,\),\_,\-,\+,\=,\{,\},\[,\],\|,\\\,\:,\;,\",\',\<,\>,\,,\.,\?,\/ ])/",$name);
    }

    if(isset($_GET["logout"])){
        $data=$_SESSION["data"];
        if($row=fetch(query($db,"SELECT*FROM `user` WHERE `number`='$data'"))){
            query($db,"INSERT INTO `data`(`number`,`move1`,`move2`,`movetime`)VALUES('$row[1]','登出','成功','$time')");
        }else{
            query($db,"INSERT INTO `data`(`number`,`move1`,`move2`,`movetime`)VALUES('未知','登出','成功','$time')");
        }
        session_unset();
        ?><script>alert("登出成功");location.href="index.php"</script><?php
    }
?>