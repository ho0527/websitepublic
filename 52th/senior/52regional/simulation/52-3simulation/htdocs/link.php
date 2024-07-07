<?php
    $db=new mysqli("localhost","admin","1234","user");
    date_default_timezone_set("Asia/Taipei");
    $time=date("Y-m-d H:i:s");
    session_start();