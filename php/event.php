<?php
include 'dbconnect.php';

$success = '';
$error = '';

$event_name = '';
$event_description = '';
$start_time = '';
$end_time = '';
$event_creator = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = trim($_POST['event_name']);
    $event_description = trim($_POST['event_description']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $event_creator = trim($_POST['event_creator']);

    if (!empty($event_name) && !empty($event_description) && !empty($start_time) && !empty($end_time) && !empty($event_creator)) {
        // Check approval from Alumni table
        $stmt = $conn->prepare("SELECT approve FROM Alumni WHERE username = ?");
        $stmt->bind_param("s", $event_creator);
        $stmt->execute();
        $stmt->bind_result($approve);
        if ($stmt->fetch()) {
            $stmt->close();
        } else {
            $stmt->close();

            // If not found in Alumni, check Student
            $stmt = $conn->prepare("SELECT approve FROM Student WHERE username = ?");
            $stmt->bind_param("s", $event_creator);
            $stmt->execute();
            $stmt->bind_result($approve);
            if (!$stmt->fetch()) {
                $error = "Event creator not found in Alumni or Student tables.";
                $stmt->close();
                $conn->close();
                return;
            }
            $stmt->close();
        }

        if ($approve == 1) {
            // Insert event
            $stmt = $conn->prepare("INSERT INTO event (event_name, event_description, start_time, end_time, event_creator) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $event_name, $event_description, $start_time, $end_time, $event_creator);

            if ($stmt->execute()) {
                $success = "Event created successfully!";
                $event_name = $event_description = $start_time = $end_time = $event_creator = '';
            } else {
                $error = "Error inserting event: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Admin hasn't approved you yet. Event creation denied.";
        }
    } else {
        $error = "All fields are required.";
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>BRACU Alumni - Events</title>
    <link rel="stylesheet" href="event.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <style>
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
          font-family: 'Roboto', sans-serif;
        }

        body {
          background-color: #f8f9fa;
          color: #333;
          line-height: 1.6;
        }

        .container {
          width: 100%;
          max-width: 1200px;
          margin: 0 auto;
          padding: 0 20px;
        }

        .navbar {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 20px 50px;
          background: rgb(235, 233, 233);
          position: fixed;
          width: 100%;
          top: 0;
          z-index: 1000;
          box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .navbar .logo img {
          width: 60px;
        }

        .nav-links {
          display: flex;
          gap: 20px;
        }

        .nav-links a {
          text-decoration: none;
          color: #333;
          font-weight: 500;
          font-size: 15px;
          transition: color 0.3s;
        }

        .nav-links a:hover {
          color: #4299e1;
        }

        main.container {
          margin-top: 120px;
        }

        .page-title {
          font-size: 36px;
          color: #2c3e50;
          margin: 30px 0;
          text-align: center;
        }

        .create-event-section {
          background-color: white;
          border-radius: 8px;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
          padding: 30px;
          margin: 50px auto;
          max-width: 800px;
        }

        .form-title {
          font-size: 24px;
          color: #2c3e50;
          margin-bottom: 5px;
          text-align: center;
        }

        .form-subtitle {
          color: #7f8c8d;
          text-align: center;
          margin-bottom: 30px;
        }

        .event-form {
          display: flex;
          flex-direction: column;
          gap: 20px;
        }

        .form-group {
          display: flex;
          flex-direction: column;
          gap: 8px;
        }

        .form-group label {
          font-weight: 500;
          color: #2c3e50;
        }

        .form-group input,
        .form-group textarea {
          padding: 12px;
          border: 1px solid #ddd;
          border-radius: 4px;
          font-size: 16px;
        }

        .form-group textarea {
          resize: vertical;
          min-height: 100px;
        }

        .submit-btn {
          background-color: #3498db;
          color: white;
          border: none;
          padding: 14px;
          border-radius: 4px;
          font-size: 16px;
          font-weight: 500;
          cursor: pointer;
          transition: background-color 0.3s;
          margin-top: 20px;
        }

        .submit-btn:hover {
          background-color: #54a9e2;
        }

        .footer {
          background-color: #2c3e50;
          color: white;
          padding: 40px 0 20px;
        }

        .footer .container {
          display: flex;
          justify-content: space-between;
          flex-wrap: wrap;
          gap: 30px;
        }

        .footer-section {
          flex: 1;
          min-width: 200px;
        }

        .footer-section h3 {
          font-size: 18px;
          margin-bottom: 15px;
          color: #ecf0f1;
        }

        .footer-section a,
        .footer-section p {
          color: #bdc3c7;
          text-decoration: none;
          margin-bottom: 10px;
          display: block;
        }

        .footer-section a:hover {
          color: #3498db;
        }

        .social-links a {
          display: flex;
          align-items: center;
          gap: 8px;
        }

        .event-box {
          padding: 15px;
          border: 1px solid #ddd;
          border-radius: 8px;
          margin-top: 20px;
          background-color: #f9f9f9;
        }

        @media (max-width: 768px) {
          .navbar {
            flex-direction: column;
            padding: 20px;
            gap: 10px;
          }
          .nav-links {
            justify-content: center;
            flex-wrap: wrap;
          }
          main.container {
            margin-top: 180px;
          }
          .footer .container {
            flex-direction: column;
            align-items: center;
            text-align: center;
          }
        }
    </style>
</head>
<body>
<header class="navbar">
    <div class="logo">
        <a href="index.php">
            <img src="../assets/logo2.png" alt="Logo">
        </a>
    </div>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="about.html">About</a>
        <a href="help.html">Help</a>
        <a href="contact.html">Contact</a>
        <a href="event.php">Events</a>
        <a href="alumni_or_student.html">Register</a>
        <a href="login.php">Login</a>
    </nav>
</header>

<main class="container">

    <h1 class="page-title">Events</h1>
<?php
    include 'dbconnect.php';
    $sql = "SELECT event_name, event_description, start_time, end_time, event_creator FROM event WHERE approve = 1 ORDER BY start_time ASC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0): ?>
        <section class="create-event-section">
            <h2 class="form-title">Upcoming Events</h2>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="event-box">
                    <h3><?php echo htmlspecialchars($row['event_name']); ?></h3>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($row['event_description']); ?></p>
                    <p><strong>Start:</strong> <?php echo htmlspecialchars($row['start_time']); ?></p>
                    <p><strong>End:</strong> <?php echo htmlspecialchars($row['end_time']); ?></p>
                    <p><strong>Created by:</strong> <?php echo htmlspecialchars($row['event_creator']); ?></p>
                </div>
            <?php endwhile; ?>
        </section>
    <?php else: ?>
        <section class="create-event-section">
            <h2 class="form-title">Events</h2>
            <p>No approved events available.</p>
        </section>
    <?php endif;
    $conn->close();
    ?>
    <section class="create-event-section">
        <h2 class="form-title">Create Events</h2>
        <p class="form-subtitle">Kindly Fill up This Form</p>

        <?php if (!empty($error)): ?>
            <p style="color: red; text-align: center;"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <p style="color: green; text-align: center;"><?php echo $success; ?></p>
        <?php endif; ?>

        <form class="event-form" action="event.php" method="POST">
            <div class="form-group">
                <label for="event_name">Event Name:</label>
                <input type="text" id="event_name" name="event_name" required value="<?php echo htmlspecialchars($event_name); ?>" />
            </div>
            <div class="form-group">
                <label for="event_description">Event Description:</label>
                <textarea id="event_description" name="event_description" required><?php echo htmlspecialchars($event_description); ?></textarea>
            </div>
            <div class="form-group">
                <label for="start_time">Start Time:</label>
                <input type="datetime-local" id="start_time" name="start_time" required value="<?php echo htmlspecialchars($start_time); ?>" />
            </div>
            <div class="form-group">
                <label for="end_time">End Time:</label>
                <input type="datetime-local" id="end_time" name="end_time" required value="<?php echo htmlspecialchars($end_time); ?>" />
            </div>
            <div class="form-group">
                <label for="event_creator">Event Creator:</label>
                <input type="text" id="event_creator" name="event_creator" required value="<?php echo htmlspecialchars($event_creator); ?>" />
            </div>
            <button type="submit" class="submit-btn">Submit Event</button>
        </form>
    </section>

    
</main>

<footer class="footer">
    <div class="container">
        <div class="footer-section">
            <h3>Contact Us</h3>
            <p>+232 54 456 7296</p>
            <p>BRACulture@gmail.com</p>
        </div>
        <div class="footer-section">
            <h3>Legal</h3>
            <a href="#">Legal Notice</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms And Conditions</a>
        </div>
        <div class="footer-section">
            <h3>Follow Us</h3>
            <div class="social-links">
                <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
                <a href="#"><i class="fab fa-facebook"></i> Facebook</a>
                <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
            </div>
        </div>
    </div>
    <div class="copyright">
        Copyright &copy; BRACU / alumni 2025
    </div>
</footer>
</body>
</html>
