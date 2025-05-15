<?php
include 'dbconnect.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and trim input
    $username = trim($_POST['username']);
    $student_id = trim($_POST['student_id']); // Capture student ID
    $password = trim($_POST['password']);
    $name = trim($_POST['name']);
    $gender = trim($_POST['gender']);
    $dob = trim($_POST['dob']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $home_address = trim($_POST['home_address']);
    $program = trim($_POST['program']);
    $session = trim($_POST['session']);
    $cgpa = trim($_POST['cgpa']);
    $designation = trim($_POST['designation']);
    $company_name = trim($_POST['company_name']);
    $job_location = trim($_POST['job_location']);

    // Validation
    $errors = [];

    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    if (empty($student_id)) {
        $errors[] = "Student ID is required.";
    } elseif (!is_numeric($student_id)) {
        $errors[] = "Student ID must be a number.";
    }    

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match('/^\d{11}$/', $phone)) {
        $errors[] = "Phone number must be exactly 11 digits.";
    }

    if (empty($program)){
        $errors[] = "Program is required.";
    }

    if (!is_numeric($cgpa) || $cgpa < 0 || $cgpa > 4) {
        $errors[] = "Invalid CGPA. Must be between 0 and 4.";
    }


    // Show errors if any
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        exit;
    }

    // Insert into User table
    $stmt = $conn->prepare("INSERT INTO User (username, password, type) VALUES (?, ?, 'alumni')");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        // Insert into Alumni table
        $stmt = $conn->prepare("INSERT INTO Alumni (username, name, gender, dob, email, phone, home_address, program, session, cgpa, designation, company_name, job_location, student_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssdsssd", $username, $name, $gender, $dob, $email, $phone, $home_address, $program, $session, $cgpa, $designation, $company_name, $job_location, $student_id);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>Alumni registration successful!</p>";
        } else {
            echo "<p style='color:red;'>Error inserting into Alumni: " . $stmt->error . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Error inserting into User: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>


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
                <img src="#" alt="BRACU Logo" class="logo">
            </div>
            <nav class="nav">
                <a href="http://localhost/student_alumni_network_system/php/index.php">HOME</a>
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
            <h1>Alumni Application</h1>
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
                        <label for="gender">Gender</label>
                        <input type="text" id="gender" name="gender" placeholder="Male/Female">
                    </div>
                    <div class="form-group">
                        <label for="dob">Date Of Birth</label>
                        <input type="date" id="dob" name="dob">
                    </div>
                </div>

                <div class="form-row">
                <div class="form-group">
                    <label for="address">Permanent Home Address</label>
                    <input type="text" id="address" name="home_address" placeholder="Home Address">
                </div>
            </section>

            <section class="form-section">
                <h2>Contact</h2>              
                <div class="form-row">
                    <div class="form-group">
                        <label for="mobile">* Mobile</label>
                        <input type="tel" id="mobile" name="phone" placeholder="Mobile" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">* Email</label>
                        <input type="email" id="email" name="email" placeholder="Email Address" required>
                    </div>
                </div>
            </section>

            <section class="form-section">
                <h2>Academics</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="programme">* Academic Programme of Study</label>
                        <input type="text" id="programme" name="program" placeholder="Programme of Study" required>
                    </div>
                    <div class="form-group">
                        <label for="session">Session</label>
                        <input type="text" id="session" name="session" placeholder="Session">
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group">
                        <label for="student_id">* Student ID</label>
                        <input type="text" id="student_id" name="student_id" placeholder="Your Student ID" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="duration">CGPA</label>
                    <input type="text" id="cgpa" name="cgpa" placeholder="Your CGPA">
                </div>
            </section>

            <section class="form-section">
                <h2>Employment</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="designation">Designation</label>
                        <input type="text" id="designation" name="designation" placeholder="Your Job Title">
                    </div>
                    <div class="form-group">
                        <label for="company">Company</label>
                        <input type="text" id="company" name="company_name" placeholder="Company Name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="location">Company Location</label>
                        <input type="text" id="location" name="job_location" placeholder="Company Location">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">* Username</label>
                        <input type="text" id="username" name="username" placeholder="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">* Password</label>
                        <input type="text" id="password" name="password" placeholder="Your Unique Password" required>
                    </div>
                    <div class="form-group">
                        <label for="password">* Confirm Your Password</label>
                        <input type="text" id="password" name="password" placeholder="" required>
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