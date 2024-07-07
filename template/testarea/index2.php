<?php
    $input="12345678900.123456789";
    if(preg_match("/^([0-9]+)((\.[0-9]+)?)$/",$input)){
        echo("Valid decimal number");
    }else{
        echo("Invalid decimal number");
    }
?>