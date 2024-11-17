<?php 
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = "Applicant";

    // Validate input fields
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        echo "Passwords do not match.";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the email already exists
        $query_check = "SELECT * FROM users WHERE email = ?";
        $stmt_check = mysqli_prepare($conn, $query_check);
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) > 0) {
            echo "Email already exists. Please use a different email.";
        } else {
            // Insert new user into the database
            $query_insert = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $query_insert);
            mysqli_stmt_bind_param($stmt_insert, "ssss", $name, $email, $hashed_password, $role);

            if (mysqli_stmt_execute($stmt_insert)) {
                echo "Registration successful! Redirecting to login page...";
                header("Refresh: 2; URL=login.php");
                exit();
            } else {
                echo "An error occurred. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #b3007d;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        .register-container input {
            width: 90%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .register-container button {
            width: 100%;
            padding: 10px;
            background-color: #ff00ae;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .register-container button:hover {
             background-color: #b3007d;
        }
        .register-container p {
            text-align: center;
            margin-top: 10px;
        }
        .register-container a {
            color: #b3007d;
            text-decoration: none;
        }
        .register-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form method="POST" action="register.php">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
