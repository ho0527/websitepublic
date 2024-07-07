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
 * remove the user cookie info and redirect to the login page.
 */
unset($_SESSION["id"]);
header('Location:' . ROOT_LEVEL . 'login.php');