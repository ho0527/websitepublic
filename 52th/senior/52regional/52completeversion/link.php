<?php
    $db=new mysqli("localhost","root","","user");
    date_default_timezone_set("Asia/Taipei");
    $time=date("Y-m-d H:i:s");
    session_start();
?>