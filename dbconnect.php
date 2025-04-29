<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project370";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check Connection
if ($conn -> connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully!"; 

?>