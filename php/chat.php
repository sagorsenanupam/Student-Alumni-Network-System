<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$currentUser = $_SESSION['username'];
$userType = $_SESSION['user_type']; // 'student' or 'alumni'
$selectedUser = $_GET['alumni'] ?? '';

// Accept message request
if (isset($_GET['accept']) && $userType === 'alumni') {
    $sender = $_GET['accept'];
    $stmt = $conn->prepare("UPDATE Message_Requests SET status = 'accepted' WHERE student_username = ? AND alumni_username = ?");
    $stmt->bind_param("ss", $sender, $currentUser);
    $stmt->execute();
    $stmt->close();
    header("Location: chat.php?alumni=$sender");
    exit();
}

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'], $_POST['receiver'])) {
    $message = trim($_POST['message']);
    $receiver = $_POST['receiver'];
    $status = null;

    if ($userType === 'student') {
        // Check if request exists
        $stmt = $conn->prepare("SELECT status FROM Message_Requests WHERE student_username = ? AND alumni_username = ?");
        $stmt->bind_param("ss", $currentUser, $receiver);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            // No request exists – insert one
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO Message_Requests (student_username, alumni_username, status) VALUES (?, ?, 'pending')");
            $stmt->bind_param("ss", $currentUser, $receiver);
            $stmt->execute();
            $stmt->close();
            $messageSent = "Message request sent to alumni. Please wait for approval.";
        } else {
            $stmt->bind_result($status);
            $stmt->fetch();
            $stmt->close();

            if ($status === 'accepted') {
                // Chat allowed
                $stmt = $conn->prepare("INSERT INTO Chat_History (sender_username, receiver_username, message) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $currentUser, $receiver, $message);
                $stmt->execute();
                $stmt->close();
                header("Location: chat.php?alumni=" . urlencode($receiver));
                exit();
            } else {
                $messageSent = "Waiting for alumni to accept your request.";
            }
        }
    } elseif ($userType === 'alumni') {
        // Alumni can always message
        $stmt = $conn->prepare("INSERT INTO Chat_History (sender_username, receiver_username, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $currentUser, $receiver, $message);
        $stmt->execute();
        $stmt->close();
        header("Location: chat.php?alumni=" . urlencode($receiver));
        exit();
    }
}

// Fetch alumni for dropdown
$alumniList = [];
if ($userType === 'student') {
    $result = $conn->query("SELECT username, name FROM Alumni WHERE approve = 1");
    while ($row = $result->fetch_assoc()) {
        $alumniList[] = $row;
    }
}

// Fetch pending requests for alumni
$requests = [];
if ($userType === 'alumni') {
    $stmt = $conn->prepare("SELECT student_username FROM Message_Requests WHERE alumni_username = ? AND status = 'pending'");
    $stmt->bind_param("s", $currentUser);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row['student_username'];
    }
    $stmt->close();
}

// Fetch messages
$messages = [];
if ($selectedUser) {
    $stmt = $conn->prepare("
        SELECT sender_username, message, time_sent FROM Chat_History 
        WHERE (sender_username = ? AND receiver_username = ?) 
           OR (sender_username = ? AND receiver_username = ?)
        ORDER BY time_sent ASC
    ");
    $stmt->bind_param("ssss", $currentUser, $selectedUser, $selectedUser, $currentUser);
    $stmt->execute();
    $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat Interface</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f7f7f7;
        }
        header {
            background: #fff;
            padding: 15px 30px;
            border-bottom: 1px solid #ccc;
        }
        .chat-container {
            display: flex;
            padding: 30px;
            gap: 20px;
        }
        .sidebar {
            width: 250px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }
        select, textarea, button {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .chat-box {
            flex: 1;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 600px;
            border: 1px solid #ccc;
        }
        .messages {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            background: #f9f9f9;
            margin-bottom: 15px;
            border-radius: 6px;
        }
        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            max-width: 70%;
        }
        .message.you {
            background: #daf1ff;
            align-self: flex-end;
        }
        .message.them {
            background: #eee;
            align-self: flex-start;
        }
        .footer {
            background: #fff;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #888;
            border-top: 1px solid #ccc;
        }
    </style>
</head>
<body>
<header>
    <h2>Welcome, <?= htmlspecialchars($currentUser) ?></h2>
</header>

<div class="chat-container">
    <div class="sidebar">
        <?php if ($userType === 'student'): ?>
            <form method="get">
                <label for="alumni">Select Alumni:</label>
                <select name="alumni" onchange="this.form.submit()">
                    <option value="">-- Choose Alumni --</option>
                    <?php foreach ($alumniList as $alum): ?>
                        <option value="<?= $alum['username'] ?>" <?= $selectedUser === $alum['username'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($alum['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php elseif ($userType === 'alumni'): ?>
            <h3>Message Requests</h3>
            <?php if ($requests): ?>
                <ul>
                    <?php foreach ($requests as $request): ?>
                        <li>
                            <?= htmlspecialchars($request) ?>
                            <a href="?accept=<?= urlencode($request) ?>">[Accept]</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No requests</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="chat-box">
        <?php if ($selectedUser): ?>
            <div class="messages">
                <?php foreach ($messages as $msg): ?>
                    <div class="message <?= $msg['sender_username'] === $currentUser ? 'you' : 'them' ?>">
                        <strong><?= $msg['sender_username'] === $currentUser ? 'You' : htmlspecialchars($msg['sender_username']) ?>:</strong><br>
                        <?= htmlspecialchars($msg['message']) ?><br>
                        <small><?= $msg['time_sent'] ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
            <form method="post">
                <input type="hidden" name="receiver" value="<?= htmlspecialchars($selectedUser) ?>">
                <textarea name="message" placeholder="Type your message..." required></textarea>
                <button type="submit">Send</button>
            </form>
            <?php if (isset($messageSent)): ?>
                <p style="color: #007bff; margin-top: 10px;"><?= $messageSent ?></p>
            <?php endif; ?>
        <?php else: ?>
            <p>Please select a user to start chatting.</p>
        <?php endif; ?>
    </div>
</div>

<div class="footer">
    &copy; <?= date('Y') ?> BRACU Alumni Chat. All rights reserved.
</div>
</body>
</html>
