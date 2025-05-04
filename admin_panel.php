<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'dbconnect.php';

<<<<<<< HEAD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $userType = $_POST['type'];
    $action = $_POST['action'];

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

// Fetch pending users
$pendingStudents = [];
$pendingAlumni = [];

$studentResult = $conn->query("SELECT username FROM Student WHERE approve = 0");
if ($studentResult) {
    while ($row = $studentResult->fetch_assoc()) {
        $pendingStudents[] = $row['username'];
    }
}

$alumniResult = $conn->query("SELECT username FROM Alumni WHERE approve = 0");
if ($alumniResult) {
    while ($row = $alumniResult->fetch_assoc()) {
        $pendingAlumni[] = $row['username'];
    }
=======
// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'], $_POST['type'], $_POST['action'])) {
        $username = $_POST['username'];
        $userType = $_POST['type'];
        $action = $_POST['action'];

        $table = ($userType === 'student') ? 'Student' : (($userType === 'alumni') ? 'Alumni' : '');
        if ($table === '') die("Invalid user type.");

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

    if (isset($_POST['event_id'], $_POST['event_action'])) {
        $eventId = $_POST['event_id'];
        $eventAction = $_POST['event_action'];

        if ($eventAction === 'approve') {
            $stmt = $conn->prepare("UPDATE event SET approve = 1 WHERE id = ?");
            $stmt->bind_param("i", $eventId);
            $stmt->execute();
            $stmt->close();
        } elseif ($eventAction === 'reject') {
            $stmt = $conn->prepare("DELETE FROM event WHERE id = ?");
            $stmt->bind_param("i", $eventId);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Fetch data
$pendingStudents = [];
$pendingAlumni = [];
$pendingEvents = [];

$studentResult = $conn->query("SELECT username FROM Student WHERE approve = 0");
while ($row = $studentResult->fetch_assoc()) {
    $pendingStudents[] = $row['username'];
}

$alumniResult = $conn->query("SELECT username FROM Alumni WHERE approve = 0");
while ($row = $alumniResult->fetch_assoc()) {
    $pendingAlumni[] = $row['username'];
}

$eventResult = $conn->query("SELECT id, event_name, event_creator FROM event WHERE approve = 0");
while ($row = $eventResult->fetch_assoc()) {
    $pendingEvents[] = $row;
>>>>>>> 2ce45a9 (edited inserted event)
}

$conn->close();
?>

<<<<<<< HEAD
<!-- [Your existing HTML layout goes here - no changes needed to the design portion] -->


=======
<!-- Start of Final HTML Output -->
>>>>>>> 2ce45a9 (edited inserted event)
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - BRACU</title>
    <style>
<<<<<<< HEAD
=======
        /* Same styles from your styled layout */
>>>>>>> 2ce45a9 (edited inserted event)
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
<<<<<<< HEAD
        button.approve {
            color: green;
        }
        button.reject {
            color: red;
        }
=======
        button.approve { color: green; }
        button.reject { color: red; }
>>>>>>> 2ce45a9 (edited inserted event)
    </style>
</head>
<body>

<div class="navbar">
    <div class="navbar-left">
        <img src="/assets/logo1.png" alt="BRACU Logo">
        <h1>Admin Panel</h1>
    </div>
    <div class="navbar-right">
        <a href="#">Home</a>
        <a href="#">Admin</a>
        <a href="#">Profile</a>
        <img src="/assets/admin.png" alt="Profile Icon">
    </div>
</div>

<div class="content">
    <!-- Students -->
    <div class="column">
        <h2>Pending Students</h2>
<<<<<<< HEAD
        <?php foreach ($pendingStudents as $username): ?>
=======
        <?php if (empty($pendingStudents)): echo "<p>No pending students.</p>"; ?>
        <?php else: foreach ($pendingStudents as $username): ?>
>>>>>>> 2ce45a9 (edited inserted event)
            <div class="user-card">
                <img src="/assets/user.png" alt="User">
                <span><?= htmlspecialchars($username) ?></span>
                <div class="action-buttons">
<<<<<<< HEAD
                    <form method="POST" action="admin_panel.php" style="display:inline;">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
                        <input type="hidden" name="type" value="student">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="approve" title="Approve">&#10004;</button>
                    </form>
                    <form method="POST" action="admin_panel.php" style="display:inline;">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
                        <input type="hidden" name="type" value="student">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="reject" title="Reject">&#10060;</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
=======
                    <form method="POST">
                        <input type="hidden" name="username" value="<?= $username ?>">
                        <input type="hidden" name="type" value="student">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="approve">&#10004;</button>
                    </form>
                    <form method="POST">
                        <input type="hidden" name="username" value="<?= $username ?>">
                        <input type="hidden" name="type" value="student">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="reject">&#10060;</button>
                    </form>
                </div>
            </div>
        <?php endforeach; endif; ?>
>>>>>>> 2ce45a9 (edited inserted event)
    </div>

    <!-- Alumni -->
    <div class="column">
        <h2>Pending Alumni</h2>
<<<<<<< HEAD
        <?php foreach ($pendingAlumni as $username): ?>
            <div class="user-card">
                <img src="/assets/alumni.png" alt="User">
                <span><?= htmlspecialchars($username) ?></span>
                <div class="action-buttons">
                    <form method="POST" action="admin_panel.php" style="display:inline;">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
                        <input type="hidden" name="type" value="alumni">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="approve" title="Approve">&#10004;</button>
                    </form>
                    <form method="POST" action="admin_panel.php" style="display:inline;">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
                        <input type="hidden" name="type" value="alumni">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="reject" title="Reject">&#10060;</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Events Placeholder -->
    <div class="column">
        <h2>Pending Events</h2>
        <div class="user-card">
            <img src="/assets/event.png" alt="Event">
            <span><strong>Tech Fair 2025</strong> by <em>creator_username</em></span>
            <div class="action-buttons">
                <button class="approve" title="Approve">&#10004;</button>
                <button class="reject" title="Reject">&#10060;</button>
            </div>
        </div>
=======
        <?php if (empty($pendingAlumni)): echo "<p>No pending alumni.</p>"; ?>
        <?php else: foreach ($pendingAlumni as $username): ?>
            <div class="user-card">
                <img src="/assets/alumni.png" alt="Alumni">
                <span><?= htmlspecialchars($username) ?></span>
                <div class="action-buttons">
                    <form method="POST">
                        <input type="hidden" name="username" value="<?= $username ?>">
                        <input type="hidden" name="type" value="alumni">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="approve">&#10004;</button>
                    </form>
                    <form method="POST">
                        <input type="hidden" name="username" value="<?= $username ?>">
                        <input type="hidden" name="type" value="alumni">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="reject">&#10060;</button>
                    </form>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- Events -->
    <div class="column">
        <h2>Pending Events</h2>
        <?php if (empty($pendingEvents)): echo "<p>No pending events.</p>"; ?>
        <?php else: foreach ($pendingEvents as $event): ?>
            <div class="user-card">
                <img src="/assets/event.png" alt="Event">
                <span><strong><?= htmlspecialchars($event['event_name']) ?></strong> by <em><?= htmlspecialchars($event['event_creator']) ?></em></span>
                <div class="action-buttons">
                    <form method="POST">
                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                        <input type="hidden" name="event_action" value="approve">
                        <button type="submit" class="approve">&#10004;</button>
                    </form>
                    <form method="POST">
                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                        <input type="hidden" name="event_action" value="reject">
                        <button type="submit" class="reject">&#10060;</button>
                    </form>
                </div>
            </div>
        <?php endforeach; endif; ?>
>>>>>>> 2ce45a9 (edited inserted event)
    </div>
</div>

</body>
</html>
