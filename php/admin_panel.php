<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'dbconnect.php';

// Handle approvals and rejections
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action === 'approve' || $action === 'reject') {
        $username = $_POST['username'];
        $userType = $_POST['type'];

        if ($userType === 'student') {
            $table = 'Student';
        } elseif ($userType === 'alumni') {
            $table = 'Alumni';
        } else {
            die("Invalid user type.");
        }

        if ($action === 'approve') {
            $stmt = $conn->prepare("UPDATE $table SET approve = 1 WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->close();
        } elseif ($action === 'reject') {
            $stmt = $conn->prepare("DELETE FROM $table WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->close();

            $stmt2 = $conn->prepare("DELETE FROM User WHERE username = ?");
            $stmt2->bind_param("s", $username);
            $stmt2->execute();
            $stmt2->close();
        }
    }

    // Event approval/rejection
    if ($action === 'approve_event') {
        $event_id = $_POST['event_id'];
        $stmt = $conn->prepare("UPDATE Event SET approve = 1 WHERE id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'reject_event') {
        $event_id = $_POST['event_id'];
        $stmt = $conn->prepare("DELETE FROM Event WHERE id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $stmt->close();
    }
}



// Fetch pending users
$pendingStudents = [];
$pendingAlumni = [];

$studentResult = $conn->query("SELECT username, gsuite_email FROM Student WHERE approve = 0");
if ($studentResult) {
    while ($row = $studentResult->fetch_assoc()) {
        $pendingStudents[] = [
            'username' => $row['username'],
            'gsuite_email' => $row['gsuite_email']
        ];
    }
}

$alumniResult = $conn->query("SELECT username, student_id FROM Alumni WHERE approve = 0");
if ($alumniResult) {
    while ($row = $alumniResult->fetch_assoc()) {
        $pendingAlumni[] = [
            'username' => $row['username'],
            'student_id' => $row['student_id']
        ];
    }
}
// Fetch pending events with creator type
$pendingEvents = [];
$eventResult = $conn->query("
    SELECT Event.id, Event.event_name, Event.event_description, Event.start_time, Event.end_time,
           Event.event_creator, User.type AS creator_type
    FROM Event
    JOIN User ON Event.event_creator = User.username
    WHERE Event.approve = 0
");
while ($row = $eventResult->fetch_assoc()) {
    $pendingEvents[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - BRACU</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
        }
        .navbar {
            background-color: #046dd6;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .navbar-left {
            display: flex;
            align-items: center;
        }
        .navbar-left img {
            height: 40px;
            margin-right: 15px;
        }
        .navbar-left h1 {
            font-size: 20px;
            margin: 0;
        }
        .navbar-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .navbar-right a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .navbar-right img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }
        .content {
            display: flex;
            padding: 20px;
            gap: 20px;
        }
        .column {
            flex: 1;
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            overflow-y: auto;
            max-height: 80vh;
        }
        .column h2 {
            margin-top: 0;
            font-size: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .user-card {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .user-card img {
            width: 40px;
            height: 40px;
            margin-right: 15px;
        }
        .user-card span {
            font-size: 16px;
        }
        .action-buttons {
            margin-left: auto;
            display: flex;
            gap: 10px;
        }
        .action-buttons button {
            border: none;
            background-color: transparent;
            font-size: 18px;
            cursor: pointer;
            padding: 5px;
            transition: transform 0.1s ease;
        }
        .action-buttons button:hover {
            transform: scale(1.2);
        }
        button.approve {
            color: green;
        }
        button.reject {
            color: red;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="navbar-left">
        <img src="../assets/logo1.png" alt="BRACU Logo">
        <h1>Admin Panel</h1>
    </div>
    <div class="navbar-right">
        <a href="index.php">Home</a>
        <a href="#">Admin</a>
        <a href="#">Profile</a>
        <img src="../assets/admin.png" alt="Profile Icon">
    </div>
</div>

<div class="content">
    <!-- Students -->
    <div class="column">
        <h2>Pending Students</h2>
        <?php foreach ($pendingStudents as $student): ?>
            <div class="user-card">
                <img src="../assets/user.png" alt="User">
                <span>
                    <?= htmlspecialchars($student['username']) ?><br>
                    <small style="color:gray;">GSuite: <?= htmlspecialchars($student['gsuite_email']) ?></small>
                </span>
                <div class="action-buttons">
                    <form method="POST" action="admin_panel.php" style="display:inline;">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($student['username']) ?>">
                        <input type="hidden" name="type" value="student">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="approve" title="Approve">&#10004;</button>
                    </form>
                    <form method="POST" action="admin_panel.php" style="display:inline;">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($student['username']) ?>">
                        <input type="hidden" name="type" value="student">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="reject" title="Reject">&#10060;</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Alumni -->
    <div class="column">
        <h2>Pending Alumni</h2>
        <?php foreach ($pendingAlumni as $alumni): ?>
            <div class="user-card">
                <img src="../assets/alumni.png" alt="User">
                <span>
                    <?= htmlspecialchars($alumni['username']) ?><br>
                    <small style="color:gray;">Student ID: <?= htmlspecialchars($alumni['student_id']) ?></small>
                </span>
                <div class="action-buttons">
                    <form method="POST" action="admin_panel.php" style="display:inline;">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($alumni['username']) ?>">
                        <input type="hidden" name="type" value="alumni">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="approve" title="Approve">&#10004;</button>
                    </form>
                    <form method="POST" action="admin_panel.php" style="display:inline;">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($alumni['username']) ?>">
                        <input type="hidden" name="type" value="alumni">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="reject" title="Reject">&#10060;</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <!-- Events -->
    <div class="column">
        <h2>Pending Event Approvals</h2>
        <?php foreach ($pendingEvents as $event): ?>
            <div class="user-card">
                <img src="../assets/event.png" alt="Event">
                <span>
                    <?= htmlspecialchars($event['event_name']) ?><br>
                    <small style="color:gray;">
                        <?= htmlspecialchars($event['start_time']) ?> to <?= htmlspecialchars($event['end_time']) ?><br>
                        Created by: <?= htmlspecialchars($event['event_creator']) ?> (<?= htmlspecialchars($event['creator_type']) ?>)<br>
                        <?= htmlspecialchars($event['event_description']) ?>
                    </small>
                </span>
                <div class="action-buttons">
                    <form method="POST" action="admin_panel.php" style="display:inline;">
                        <input type="hidden" name="event_id" value="<?= htmlspecialchars($event['id']) ?>">
                        <input type="hidden" name="action" value="approve_event">
                        <button type="submit" class="approve" title="Approve">&#10004;</button>
                    </form>
                    <form method="POST" action="admin_panel.php" style="display:inline;">
                        <input type="hidden" name="event_id" value="<?= htmlspecialchars($event['id']) ?>">
                        <input type="hidden" name="action" value="reject_event">
                        <button type="submit" class="reject" title="Reject">&#10060;</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
