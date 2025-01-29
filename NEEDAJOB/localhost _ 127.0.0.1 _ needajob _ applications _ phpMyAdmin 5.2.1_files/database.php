<?php
// database.php

$host = '127.0.0.1'; // localhost
$port = '3306'; // Default MySQL port
$username = 'root'; // XAMPP default username
$password = ''; // Default XAMPP password (empty)
$dbname = 'NEEDAJOB'; // Replace with your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
