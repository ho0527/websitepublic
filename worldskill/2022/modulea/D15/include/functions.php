<?php

session_start();

/**
 * This function checks to see if a user is logged in and will
 * redirect to the login page if required.
 */
function checkLogin(){
    if (!isset($_SESSION['id'])){
        header('Location:' . ROOT_LEVEL . 'login.php');
        exit;
    }
}

/**
 * This method attempts to connect to the database and returns a PDO
 * connection object.
 *
 * @return PDO
 */
function dbConnect(){
    try{
        $pdo = new PDO('mysql:host=localhost;dbname=task8;charset=utf8','root', '');
        return $pdo;
    } catch (Exception $e){
        die($e->getMessage());
    }
}

/**
 * This function returns an array of information about the user who
 * is logged in.
 *
 * @return mixed
 */

function query($db,$query,$data=[]){
    $prepare=$db->prepare($query);
    $prepare->execute($data);
    return $prepare->fetchAll();
}