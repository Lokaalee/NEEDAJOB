<?php
session_start();

// Check if the user is logged in by checking the session variable
if (isset($_SESSION['user_id'])) {
    // Destroy the session to log the user out
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session

    // Redirect to index.php after logout
    header("Location: index.php");
    exit(); // Stop further execution
} else {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}
?>