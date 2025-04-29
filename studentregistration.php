<?php
include 'dbconnect.php'; // Update the path if needed
// Connect to the database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data safely
    $name = $_POST['name'] ?? '';
    $department = $_POST['department'] ?? NULL; // optional
    $gsuite_email = $_POST['gsuite_email'] ?? '';
    $student_id = $_POST['student_id'] ?? ''; // Collect student_id
    // $username = $_POST['username'] ?? '';     // Collect username
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basic validation
    if (empty($name) || empty($gsuite_email) || empty($password)) {
        echo "<script>alert('Name, GSuite Email, and Password are required fields!');</script>";
        exit();
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
        exit();
    }


    // Prepare the SQL statement
    $sql = "INSERT INTO studentregistration (Name, Department, GSuiteEmail, StudentID, Password) VALUES ( ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "sssss", $name, $department, $gsuite_email, $student_id, $password);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Student registered successfully!');</script>";
        } else {
            // Handle unique constraint errors
            $error = mysqli_stmt_error($stmt);
            if (strpos($error, 'Duplicate entry') !== false) {
                echo "<script>alert('Error: GSuite Email or Password already exists!');</script>";
            } else {
                echo "<script>alert('Error: " . $error . "');</script>";
            }
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error preparing statement: " . mysqli_error($conn) . "');</script>";
    }
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
                <a href="#">HOME</a>
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
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="Full Name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="student-id">Student ID</label>
                        <input type="text" id="student-id" name="student_id" placeholder="Student ID">
                    </div>
                </div>
            </section>

            <section class="form-section">
                    <div class="form-group">
                        <label for="gsuite-email">G-Suite Email Address</label>
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
                    <!-- <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="username">
                    </div> -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Your Unique Password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Your Password</label>
                        <input type="password" id="confirm_password" name="confirm_password">

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