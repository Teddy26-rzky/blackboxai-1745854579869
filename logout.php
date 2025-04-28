<?php
// logout.php - Proses logout user

session_start();
session_unset();
session_destroy();

header('Location: login.php');
exit;
?>
