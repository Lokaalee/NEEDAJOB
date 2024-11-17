<?php
session_start(); // Start the session to retrieve user_id

// Database connection details
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'NEEDAJOB'; // Your database name

$conn = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve job_id from URL (apply.php?job_id=1)
if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id']; // Get the job_id from the URL
} else {
    echo '<div class="message error">Job ID not specified.</div>';
    exit;
}

// Fetch job details from the jobs table based on the job_id
$sql = "SELECT * FROM jobs WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_id); // "i" means integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $job = $result->fetch_assoc(); // Job details
} else {
    echo '<div class="message error">Job not found.</div>';
    exit;
}

// Check if user is logged in (user_id should be in session)
if (!isset($_SESSION['user_id'])) {
    echo '<div class="message info">You need to log in to apply for a job. <a href="login.php">Click here to login</a></div>';
    exit;
}

// Get user_id from session (ensure user is logged in)
$user_id = $_SESSION['user_id']; // Get the logged-in user's ID from the session

// Check if the user has already applied for this job
$sql_check = "SELECT * FROM applications WHERE job_id = ? AND user_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $job_id, $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo '<div class="message error">You have already applied for this job.</div>';
    exit;
}

// Handle the application form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure that a resume is uploaded
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $resume_path = 'uploads/' . basename($_FILES['resume']['name']); // Path to store the uploaded resume
        move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path); // Move the uploaded file to the folder

        // Insert the application data into the applications table
        $sql_apply = "INSERT INTO applications (job_id, user_id, Job, Company, resume_path, applied_date) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt_apply = $conn->prepare($sql_apply);
        $stmt_apply->bind_param("iisss", $job_id, $user_id, $job['titles'], $job['company'], $resume_path);

        if ($stmt_apply->execute()) {
            echo '<div class="message success">Your application has been submitted successfully.</div>';
        } else {
            echo '<div class="message error">Failed to apply. Please try again later.</div>';
        }
    } else {
        echo '<div class="message error">Please upload a valid resume.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* General Page Styling */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #b3007d;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: purple;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            color: purple;
        }

        input[type="file"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #b3007d;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: purple;
        }

        /* Styling for Messages */
        .message {
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 16px;
        }

        .message.success {
            background-color: #d4edda;
            color: Purple;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Apply for Job: <?php echo htmlspecialchars($job['titles']); ?></h2>
        <form action="apply_job.php?job_id=<?php echo $job_id; ?>" method="POST" enctype="multipart/form-data">
            <label for="resume">Upload Your Resume:</label>
            <input type="file" name="resume" id="resume" required>
            <input type="submit" value="Submit Application">
        </form>
    </div>
</body>
</html>
