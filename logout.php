<?php
//start session to acress it 
session_start();
// destroy all the session 
$_SESSION = [];


if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}



//destroy all session data 
session_destroy();
header('location: index.php');
exit();



?>