<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user inputs
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title']);
    $job_description = mysqli_real_escape_string($conn, $_POST['job_description']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $posted_by = $_SESSION['user_id'];  // ID of the user who is posting the job

    // Insert the job posting into the database
    $sql = "INSERT INTO jobs (titles, description, company, location, salary, posted_by) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $job_title, $job_description, $company, $location, $salary, $posted_by);

    if ($stmt->execute()) {
        echo "Job posted successfully!";
    } else {
        echo "Error posting job. Please try again.";
    }
}
?>
