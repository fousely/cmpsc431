<?php
session_start();
unset($_SESSION);
session_destroy();
session_write_close();

$host  = $_SERVER['HTTP_HOST'];
$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = 'myAccount.php';
header("Location: http://$host$uri/$extra");
die;
?>
