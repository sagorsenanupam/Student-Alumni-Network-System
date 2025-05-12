<?php
include 'dbconnect.php'; // Ensure correct path

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data safely
    $name = trim($_POST['name'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $gsuite_email = trim($_POST['gsuite_email'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    $errors = [];

    // Required fields check
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($gsuite_email)) {
        $errors[] = "Gsuite email is required.";
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@g\.bracu\.ac\.bd$/', $gsuite_email)) {
        $errors[] = "Invalid Gsuite email format. Must be in the format name@g.bracu.ac.bd";
    }

    if (empty($student_id)) {
        $errors[] = "Student ID is required.";
    } elseif (!preg_match('/^\d{6}$/', $student_id)) {
        $errors[] = "Student ID must be exactly 6 digits.";
    }

    // If errors, show all
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        exit();
    }

    // Insert into User table
    $stmt_user = $conn->prepare("INSERT INTO User (username, password, type) VALUES (?, ?, 'student')");
    $stmt_user->bind_param("ss", $username, $password);

    if ($stmt_user->execute()) {
        // Insert into Student table
        $stmt_student = $conn->prepare("INSERT INTO Student (id, username, name, department, gsuite_email) VALUES (?, ?, ?, ?, ?)");
        $stmt_student->bind_param("issss", $student_id, $username, $name, $department, $gsuite_email);

        if ($stmt_student->execute()) {
            echo "<p style='color:green;'>Student registered successfully!</p>";
        } else {
            echo "<p style='color:red;'>Error inserting into Student: " . $stmt_student->error . "</p>";
        }

        $stmt_student->close();
    } else {
        echo "<p style='color:red;'>Error inserting into User: " . $stmt_user->error . "</p>";
    }

    $stmt_user->close();
    $conn->close();
}
?>




<!-- HTML Part -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRACU Alumni - Registration Form</title>
    <link rel="stylesheet" href="application.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo-container">
                <img src="brac.png" alt="BRACU Alumni Logo" class="logo">
            </div>
            <nav class="nav">
                <a href="index.php">HOME</a>
                <a href="#" class="active">APPLY</a>
                <a href="#">ABOUT</a>
                <a href="#">HELP</a>
                <a href="#">CONTACT</a>
                <a href="#">EVENTS</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="form-header">
            <h1>Student Registration</h1>
            <p class="form-notice">Complete all questions.</p>
            <p class="form-notice">Please note that your application may not be processed if you leave any questions unanswered.</p>
        </div>

        <form class="application-form" method="POST" action="">
            <section class="form-section">
                <h2>Personal Information of Applicant</h2>
                <div>* denotes the required fields</div>

                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="Full Name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="student-id">* Student ID</label>
                        <input type="text" id="student-id" name="student_id" placeholder="Student ID" required>
                    </div>
                </div>
            </section>

            <section class="form-section">
                    <div class="form-group">
                        <label for="gsuite-email">* G-Suite Email Address</label>
                        <input type="email" id="gsuite-email" name="gsuite_email" placeholder="G-Suite Email Address">
                    </div>
            </section>

            <section class="form-section">
                <h2>Academics</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="department">Department</label>
                        <input type="text" id="department" name="department" placeholder="Your Department">
                    </div>
                </div>
            </section>

            <section class="form-section">
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">* Username</label>
                        <input type="text" id="username" name="username" placeholder="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">* Password</label>
                        <input type="password" id="password" name="password" placeholder="Your Unique Password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">* Confirm Your Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>

                    </div>
                </div>
            </section>

            <div class="form-actions">
                <button type="submit" class="btn-next">Submit <i class="fas fa-paper-plane"></i></button>
            </div>
        </form>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>+233 54 456 7298</p>
                <p>bracualumi@gmail.com</p>
            </div>
            
            <div class="footer-section">
                <h3>Legal</h3>
                <a href="#">Legal Notice</a>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms And Conditions</a>
            </div>
            
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            Copyright &copy; BRACU Alumni 2025
        </div>
    </footer>
</body>
</html>