<?php
include 'dbconnect.php'; // Connect to the database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data safely
    $name = $_POST['name'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $student_id = $_POST['student_id'] ?? '';
    $place_of_birth = $_POST['place_of_birth'] ?? '';
    $hometown = $_POST['hometown'] ?? '';
    $country = $_POST['country'] ?? '';
    $address = $_POST['address'] ?? '';
    $district = $_POST['district'] ?? '';
    $residence_period = $_POST['residence_period'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $email = $_POST['email'] ?? '';
    $gsuite_email = $_POST['gsuite_email'] ?? '';
    $programme = $_POST['programme'] ?? '';
    $level = $_POST['level'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $designation = $_POST['designation'] ?? '';
    $company = $_POST['company'] ?? '';
    $location = $_POST['location'] ?? '';
    $working_since = $_POST['working_since'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Set username if empty
    if (empty($username)) {
        $username = $student_id;
    }

    // Hash the password
    #$hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare an SQL statement with placeholders
    $sql = "INSERT INTO alumniregistration 
    (name, gender, dob, student_id, place_of_birth, hometown, country, address, district, residence_period, telephone, mobile, email, gsuite_email, programme, level, duration, designation, company, location, working_since, username, password)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters: 23 strings = 23 's'
        mysqli_stmt_bind_param($stmt, 
            "sssssssssssssssssssssss", 
            $name, $gender, $dob, $student_id, $place_of_birth, $hometown, $country, $address, $district, $residence_period,
            $telephone, $mobile, $email, $gsuite_email, $programme, $level, $duration, $designation, $company, $location,
            $working_since, $username, $password
        );

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Application submitted successfully!');</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_stmt_error($stmt) . "');</script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error preparing statement: " . mysqli_error($conn) . "');</script>";
    }
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
            <h1>Alumni Application</h1>
            <p class="form-notice">Complete all questions.</p>
            <p class="form-notice">Please note that your application may not be processed if you leave any questions unanswered.</p>
        </div>

        <form class="application-form" method="POST" action="">
            <section class="form-section">
                <h2>Personal Information of Applicant</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">First Name</label>
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
                    <div class="form-group">
                        <label for="student-id">Student ID</label>
                        <input type="text" id="student-id" name="student_id" placeholder="Student ID">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="place-of-birth">Place Of Birth</label>
                        <input type="text" id="place-of-birth" name="place_of_birth" placeholder="Place of Birth">
                    </div>
                    <div class="form-group">
                        <label for="hometown">Hometown</label>
                        <input type="text" id="hometown" name="hometown" placeholder="Hometown">
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" placeholder="Country">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Permanent Home Address</label>
                    <input type="text" id="address" name="address" placeholder="Home Address">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="district">District / Region</label>
                        <input type="text" id="district" name="district" placeholder="District / Region">
                    </div>
                    <div class="form-group">
                        <label for="residence-period">Residence Period</label>
                        <input type="text" id="residence-period" name="residence_period" placeholder="e.g., 4 years">
                    </div>
                </div>
            </section>

            <section class="form-section">
                <h2>Contact</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="telephone">Telephone</label>
                        <input type="tel" id="telephone" name="telephone" placeholder="Telephone">
                    </div>
                    <div class="form-group">
                        <label for="mobile">Mobile</label>
                        <input type="tel" id="mobile" name="mobile" placeholder="Mobile">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Email Address">
                    </div>
                    <div class="form-group">
                        <label for="gsuite-email">G-Suite Email Address</label>
                        <input type="email" id="gsuite-email" name="gsuite_email" placeholder="G-Suite Email Address">
                    </div>
                </div>
            </section>

            <section class="form-section">
                <h2>Academics</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="programme">Academic Programme of Study</label>
                        <input type="text" id="programme" name="programme" placeholder="Programme of Study">
                    </div>
                    <div class="form-group">
                        <label for="level">Level of Study</label>
                        <input type="text" id="level" name="level" placeholder="Level">
                    </div>
                </div>

                <div class="form-group">
                    <label for="duration">Duration Of Study</label>
                    <input type="text" id="duration" name="duration" placeholder="Duration">
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
                        <input type="text" id="company" name="company" placeholder="Company Name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="location">Company Location</label>
                        <input type="text" id="location" name="location" placeholder="Company Location">
                    </div>
                    <div class="form-group">
                        <label for="working-since">Working Since</label>
                        <input type="text" id="working-since" name="working_since" placeholder="e.g., 2022">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="text" id="password" name="password" placeholder="Your Unique Password">
                    </div>
                    <div class="form-group">
                        <label for="password">Confirm Your Password</label>
                        <input type="text" id="password" name="password" placeholder="">
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