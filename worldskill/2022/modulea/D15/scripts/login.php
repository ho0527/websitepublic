<?php

/**
 * define the number of ../ to get to the root folder
 */
define('ROOT_LEVEL', '../');

/**
 * require the general functions file
 */
require(ROOT_LEVEL . 'include/functions.php');

/*
 * look up the user
 */

$pdo = dbConnect();
$stmt = query($pdo,"SELECT*FROM `users` WHERE `username`=? AND `password`=?",[$_POST["username"],md5($_POST['password'])]);

/*
 * if no user is found, redirect to the login page with an error,
 * otherwise, save the info in a cookie
 */
if(!isset($_SESSION["error"])){ $_SESSION["error"]=0; }

if (!$stmt){
    $_SESSION["error"]=$_SESSION["error"]+1;
    if($_SESSION["error"]<3){
        ?><script>alert("帳號或密碼錯誤");location.href="../login.php"</script><?php
    }else{
        ?><script>alert("帳號或密碼錯誤");location.href="../error.php"</script><?php
    }
    exit;
} else {
    $_SESSION["id"]=$stmt[0];
    // setcookie('logged_in', serialize(['id' => $user['id'], 'username' => $user['username']]), 0, '/');
    header('Location:' . ROOT_LEVEL . 'index.php');
    exit;
}

