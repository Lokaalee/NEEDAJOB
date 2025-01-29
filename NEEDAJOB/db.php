<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "needajob";

// Create connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<div class='message error'>Database Connection Failed: " . $conn->connect_error . "</div>");
}
?>
