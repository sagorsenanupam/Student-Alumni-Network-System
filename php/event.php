<?php
include 'dbconnect.php';

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = trim($_POST['event_name']);
    $event_description = trim($_POST['event_description']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $event_creator = trim($_POST['event_creator']);

    if (!empty($event_name) && !empty($event_description) && !empty($start_time) && !empty($end_time) && !empty($event_creator)) {

        // Check if event_creator exists in 'user' table
        $check_stmt = $conn->prepare("SELECT 1 FROM user WHERE username = ?");
        $check_stmt->bind_param("s", $event_creator);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            // User exists, insert event
            $stmt = $conn->prepare("INSERT INTO event (event_name, event_description, start_time, end_time, event_creator) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $event_name, $event_description, $start_time, $end_time, $event_creator);

            if ($stmt->execute()) {
                $success = "Event created successfully!";
            } else {
                $error = "Error inserting event: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error = "Event creator not found in database.";
        }

        $check_stmt->close();
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
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo-container">
                <img src="brac.png" alt="BRACU Alumni Logo" class="logo"/>
            </div>
            <nav class="nav">
                <a href="index.html">HOME</a>
                <a href="#" class="active">APPLY</a>
                <a href="#">ABOUT</a>
                <a href="#">HELP</a>
                <a href="#">CONTACT</a>
                <a href="#">EVENTS</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1 class="page-title">Events</h1>

        <!-- Create Event Section -->
        <section class="create-event-section">
            <h2 class="form-title">Create Events</h2>
            <p class="form-subtitle">Kindly Fill up This Form</p>
            <form class="event-form" action="event.php" method="POST">
                <!-- ID is auto-increment in DB, so no input needed -->
            
                <div class="form-group">
                    <label for="event_name">Event Name:</label>
                    <input type="text" id="event_name" name="event_name" required />
                </div>
            
                <div class="form-group">
                    <label for="event_description">Event Description:</label>
                    <textarea id="event_description" name="event_description" rows="4" required></textarea>
                </div>
            
                <div class="form-group">
                    <label for="start_time">Start Time:</label>
                    <input type="datetime-local" id="start_time" name="start_time" required />
                </div>
            
                <div class="form-group">
                    <label for="end_time">End Time:</label>
                    <input type="datetime-local" id="end_time" name="end_time" required />
                </div>
            
                <div class="form-group">
                    <label for="event_creator">Event Creator:</label>
                    <input type="text" id="event_creator" name="event_creator" required />
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
