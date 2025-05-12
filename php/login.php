<?php
include 'dbconnect.php';
session_start();

$adminUsername = 'admin';
$adminPassword = 'admin';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Admin login
    if ($username === $adminUsername && $password === $adminPassword) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_panel.php");
        exit();
    }

    // Check user credentials in user table
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $userResult = $stmt->get_result();

    if ($userResult && $userResult->num_rows === 1) {
        $user = $userResult->fetch_assoc();

        if ($password === $user['password']) {
            // Password matched
            if ($user['type'] === 'alumni') {
                // Now check approval status from alumni table
                $stmt2 = $conn->prepare("SELECT * FROM alumni WHERE username = ?");
                $stmt2->bind_param("s", $username);
                $stmt2->execute();
                $alumniResult = $stmt2->get_result();

                if ($alumniResult && $alumniResult->num_rows === 1) {
                    $alumni = $alumniResult->fetch_assoc();

                    if ($alumni['approve'] == 1) {
                        // Approved, proceed to profile
                        $_SESSION['username'] = $username;
                        $_SESSION['user_type'] = 'alumni';
                        header("Location: loginprofile.php");
                        exit();
                    } else {
                        $error = "Admin has not approved you yet.";
                    }
                } else {
                    $error = "Alumni data not found.";
                }

                $stmt2->close();
            } else {
                $error = "You are not an alumni.";
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRACU Alumni - Login</title>
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
        <img src="../assets/image16.png" alt="Graduate">
    </div>
    <div class="login-container">
        <h1>Log In</h1>
        <p class="welcome-text">Welcome back. Please enter your credentials.</p>

        <!-- Show error message if any -->
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
