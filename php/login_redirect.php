<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['user_type']) || !isset($_SESSION['redirect_target'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$user_type = $_SESSION['user_type'];
$redirect_target = $_SESSION['redirect_target'];

// Clear the redirect target for safety
unset($_SESSION['redirect_target']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <meta http-equiv="refresh" content="1;url=<?= htmlspecialchars($redirect_target) ?>">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .popup {
            background: #ffffff;
            padding: 30px 50px;
            border: 2px solid #007bff;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.2);
        }

        .popup h2 {
            margin-bottom: 10px;
            color: #007bff;
        }

        .popup p {
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="popup">
        <h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>
        <p>You are logged in as <strong><?= htmlspecialchars($user_type) ?></strong>.</p>
    </div>
</body>
</html>
