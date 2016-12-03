<?php
include 'functions.php';

session_start();
unset($_SESSION);
session_destroy();
session_write_close();

goToPage('myAccount.php');
die;
?>
