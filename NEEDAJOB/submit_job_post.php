<?php
session_start();

// Include database connection
include 'db.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user inputs
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title']);
    $job_description = mysqli_real_escape_string($conn, $_POST['job_description']);
    $company = mysqli_real_escape_string($conn, $_POST['company']); // Fixed case
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);

    // Insert the job posting into the database
    $sql = "INSERT INTO jobs (job_title, job_description, company, location, salary) 
            VALUES (?, ?, ?, ?, ?)";  // Removed extra placeholder

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $job_title, $job_description, $company, $location, $salary);  // Adjusted parameter types

    if ($stmt->execute()) {
        echo "<div class='message success'>Job posted successfully!</div>";
    } else {
        echo "<div class='message error'>Error posting job. Please try again.</div>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
