<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // If already logged in, redirect to index.php (dashboard)
    header("Location: index.php");
    exit();
}

// If the login form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection and login processing
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'needajob'; // Your database name

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Sanitize input
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $password = mysqli_real_escape_string($conn, $password);

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            
            // Check if there's a redirect URL set
            if (isset($_GET['redirect'])) {
                // Redirect to the page specified in the redirect parameter
                header("Location: " . $_GET['redirect']);
            } else {
                // If no redirect is set, go to index.php (dashboard)
                header("Location: index.php");
            }
            exit();
        } else {
            $_SESSION['error'] = 'Invalid email or password.';
        }
    } else {
        $_SESSION['error'] = 'Invalid email or password.';
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
        .login-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 10px;
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
        .login-container p {
            text-align: center;
            margin-top: 10px;
        }
        .login-container a {
            color: #b3007d;
            text-decoration: none;
        }
        .login-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        
        <?php
        // Display error message if login fails
        if (isset($_SESSION['error'])) {
            echo '<p style="color:red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . $_GET['redirect'] : ''; ?>" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
