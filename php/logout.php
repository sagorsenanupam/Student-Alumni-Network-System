<?php
include_once 'dbconnect.php'; // Include your database connection file
session_start();
session_unset();
session_destroy();
header("Location: index.php");
exit();
?>