<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Initialize a variable for success message
$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize form input
    $job_title = trim(mysqli_real_escape_string($conn, $_POST['job_title']));
    $job_description = trim(mysqli_real_escape_string($conn, $_POST['job_description']));
    $company = trim(mysqli_real_escape_string($conn, $_POST['company']));
    $location = trim(mysqli_real_escape_string($conn, $_POST['location']));
    $salary = trim(mysqli_real_escape_string($conn, $_POST['salary']));

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO jobs (job_title, job_description, company, location, salary) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $job_title, $job_description, $company, $location, $salary);

    if ($stmt->execute()) {
        $success_message = "Job posted successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Job</title>
    <style>
        /* General page styling */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: purple;
            color: white;
            text-align: center;
            padding: 20px;
        }

        h1 {
            margin: 0;
        }

        /* Main container */
        main {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            background-color: #fff;
        }

        /* Form container */
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Form elements */
        label {
            font-size: 1rem;
            margin: 10px 0 5px;
            display: block;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        textarea {
            height: 150px;
            resize: vertical;
        }

        /* Submit button */
        button {
            width: 100%;
            padding: 12px;
            background-color: #b3007d;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            cursor: pointer;
        }

        button:hover {
            background-color: purple;
        }

        /* Success & error messages */
        .message {
            text-align: center;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <header>
        <h1>Post Job</h1>
    </header>

    <main>
        <form action="post_job.php" method="POST">
            <?php if (!empty($success_message)) { ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php } ?>

            <?php if (!empty($error_message)) { ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php } ?>

            <label for="job_title">Job Title:</label>
            <input type="text" id="job_title" name="job_title" required>

            <label for="job_description">Job Description:</label>
            <textarea id="job_description" name="job_description" required></textarea>

            <label for="company">Company:</label>
            <input type="text" id="company" name="company" required>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required>

            <label for="salary">Salary:</label>
            <input type="text" id="salary" name="salary" required>

            <button type="submit">Post Job</button>
        </form>
    </main>
</body>
</html>
