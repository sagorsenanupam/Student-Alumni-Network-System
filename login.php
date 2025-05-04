<?php
<<<<<<< HEAD
include 'dbconnect.php'; // Ensure correct path
session_start();

// Hardcoded credentials (you can connect this to a database later)
$adminUsername = "admin";
$adminPassword = "admin";

// Handle login form submission
=======
include 'dbconnect.php';
session_start();

$message = '';

>>>>>>> 2ce45a9 (edited inserted event)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

<<<<<<< HEAD
    if ($username === $adminUsername && $password === $adminPassword) {
=======
    // Check for admin login
    if ($username === 'admin' && $password === 'admin') {
>>>>>>> 2ce45a9 (edited inserted event)
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_panel.php");
        exit();
    } else {
<<<<<<< HEAD
        $error = "Invalid username or password.";
=======
        // Check for approved users in the User table
        $stmt = $conn->prepare("SELECT password FROM User WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($Password);
            $stmt->fetch();

            if ($password === $Password) {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['username'] = $username;
                $message = "Successfully logged in as user.";
            } else {
                $message = "Incorrect password.";
            }
        } else {
            $message = "User not found or not approved.";
        }

        $stmt->close();
>>>>>>> 2ce45a9 (edited inserted event)
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BRACU Alumni - Login</title>
<<<<<<< HEAD
    <style>
        body {
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Roboto', sans-serif;
        }
        .login-page {
            display: flex;
            width: 800px;
            height: 500px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .login-image {
            flex: 1;
            background-color: #fff;
        }
        .login-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .login-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
        }
        h1 {
            font-size: 35px;
            margin-bottom: 10px;
            color: #2d3748;
        }
        .welcome-text {
            font-size: 14px;
            color: #718096;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-size: 14px;
            display: block;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #4299e1;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="login-page">
    <div class="login-image">
        <img src="/assets/image16.png" alt="Graduate">
    </div>
    <div class="login-container">
        <h1>Log In</h1>
        <p class="welcome-text">Welcome back. Please enter your credentials.</p>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</div>
</body>
</html>
=======
    <link rel="stylesheet" href="../Home Page/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-page">
        <div class="login-image">
            <img src="../assets/image16.png" alt="Graduate Image">
        </div>
        <div class="login-container">
            <div class="login-card">
                <h1>Log In</h1>
                <p class="welcome-text">Welcome Back.<br>Please Enter Your Details.</p>

                <?php if (!empty($message)): ?>
                    <p style="color: red; text-align:center;"><?php echo $message; ?></p>
                <?php endif; ?>

                <form class="login-form" method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" placeholder="Your username" required>
                    </div>

                    <div class="form-group password-group">
                        <label for="password">Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" placeholder="**********" required>
                            <span class="toggle-password" onclick="togglePassword()">👁️</span>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <button type="submit" class="login-btn">Login</button>
                </form>

                <p class="signup-text">Don't have an account? <a href="../Home Page/alumni_or_student.html" class="signup-link">Sign Up</a></p>
            </div>

            <footer class="copyright">
                Copyright &copy; BRACU Alumni 2025
            </footer>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>
>>>>>>> 2ce45a9 (edited inserted event)
