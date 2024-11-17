<?php
session_start();
include 'db.php';

$redirect_url = 'index.php'; 
if (isset($_GET['job_id'])) {
    $redirect_url = 'apply_job.php?job_id=' . $_GET['job_id']; 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL query with a placeholder for email
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email); 
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Check if the password matches
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['ID'];  
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Check if a job_id is present in the URL and whether the user has applied for this job
            if (isset($_GET['job_id'])) {
                $job_id = $_GET['job_id'];

                $check_application_query = "SELECT * FROM applications WHERE job_id = ? AND user_id = ?";
                $stmt_check = mysqli_prepare($conn, $check_application_query);
                mysqli_stmt_bind_param($stmt_check, "ii", $job_id, $_SESSION['user_id']);
                mysqli_stmt_execute($stmt_check);
                $application_result = mysqli_stmt_get_result($stmt_check);

                if (mysqli_num_rows($application_result) > 0) {
                    echo "You have already applied for this job.";
                    header("Refresh: 2; URL=$redirect_url");  
                    exit();
                } else {
                    header("Location: $redirect_url");
                    exit();
                }
            } else {
                header("Location: $redirect_url");
                exit();
            }
        } else {
            echo "Invalid credentials.";
        }
    } else {
        echo "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* General Styling */
        body {
            font-family: "poppins", sans-serif;
            background-color: #b3007d;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Login Container */
        .login-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-container input {
            width: 90%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #ff00ae;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #b3007d;
        }

        .login-footer {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
        }

        .login-footer a {
            color: #6a38c2;
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="login-footer">
            <p>Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>
</body>
</html>
