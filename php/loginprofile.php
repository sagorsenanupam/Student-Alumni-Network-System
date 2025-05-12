<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'alumni') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch alumni details from the database
$stmt = $conn->prepare("SELECT * FROM alumni WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Alumni record not found.";
    exit();
}

$alumni = $result->fetch_assoc();

// Update alumni details if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $home_address = $_POST['home_address'];
    $designation = $_POST['designation'];
    $company_name = $_POST['company_name'];
    $job_location = $_POST['job_location'];

    // Prepare an update statement
    $update_stmt = $conn->prepare("UPDATE alumni SET email = ?, phone = ?, home_address = ?, designation = ?, company_name = ?, job_location = ? WHERE username = ?");
    $update_stmt->bind_param("sssssss", $email, $phone, $home_address, $designation, $company_name, $job_location, $username);

    if ($update_stmt->execute()) {
        // On successful update, reload the page to reflect the changes
        header("Location: loginprofile.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Alumni Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Font Awesome for icons -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      padding-top: 80px;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    .navbar {
      position: fixed;
      top: 0;
      width: 100%;
      background-color: #ffffff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 60px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }

    .navbar .logo img {
      height: 50px;
    }

    .nav-links {
      display: flex;
      gap: 20px;
    }

    .nav-links a {
      color: #333;
      font-weight: 500;
      font-size: 15px;
      transition: color 0.3s;
    }

    .nav-links a:hover {
      color: #4299e1;
    }

    .profile-container {
      max-width: 800px;
      margin: 0 auto;
      background-color: #ffffff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .profile-header {
      text-align: center;
      margin-bottom: 30px;
    }

    .profile-header img {
      width: 130px;
      height: 130px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #4299e1;
      margin-bottom: 15px;
    }

    .profile-header h2 {
      margin-bottom: 5px;
      color: #333;
    }

    .profile-header p {
      color: #666;
      font-size: 15px;
    }

    .profile-info .info-group {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: #f9f9f9;
      padding: 15px 20px;
      margin-bottom: 15px;
      border-radius: 8px;
    }

    .profile-info label {
      font-weight: bold;
      color: #444;
      flex: 1;
    }

    .profile-info span {
      color: #555;
      flex: 2;
    }

    .edit-btn {
      background-color: #4299e1;
      border: none;
      color: white;
      padding: 6px 14px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .edit-btn:hover {
      background-color: #327dc5;
    }

    .footer {
      background-color: #2c3e50;
      color: white;
      padding: 40px 60px 20px;
      margin-top: 60px;
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

    .footer-section p,
    .footer-section a {
      color: #bdc3c7;
      margin-bottom: 10px;
      display: block;
      text-decoration: none;
    }

    .footer-section a:hover {
      color: #3498db;
    }

    .social-icons {
      display: flex;
      gap: 15px;
    }

    .social-icons a {
      color: white;
      font-size: 20px;
    }

    .copyright {
      text-align: center;
      margin-top: 40px;
      padding-top: 20px;
      border-top: 1px solid #34495e;
      color: #bdc3c7;
      font-size: 14px;
    }

    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
        padding: 15px 20px;
      }

      .nav-links {
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
      }

      .footer .container {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <header class="navbar">
    <div class="logo">
      <a href="index.html">
        <img src="../assets/logo2.png" alt="Logo" />
      </a>
    </div>
    <nav class="nav-links">
      <a href="index.html">Home</a>
      <a href="about.html">About</a>
      <a href="help.html">Help</a>
      <a href="contact.html">Contact</a>
      <a href="/php/event.php">Events</a>
      <a href="alumni_or_student.html">Register</a>
      <a href="/php/login.php">Login</a>
    </nav>
  </header>

  <!-- Profile Section -->
  <div class="profile-container">
    <div class="profile-header">
      <img src="../assets/alumni.png" alt="Profile Photo" />
      <h2><?= htmlspecialchars($alumni['name']) ?></h2>
      <p>Alumni</p>
    </div>

    <div class="profile-info">
      <form method="POST" action="loginprofile.php">
        <div class="info-group"><label>Username:</label><span><?= htmlspecialchars($alumni['username']) ?></span></div>
        <div class="info-group"><label>Student ID:</label><span><?= htmlspecialchars($alumni['student_id']) ?></span></div>
        <div class="info-group"><label>Name:</label><span><?= htmlspecialchars($alumni['name']) ?></span></div>
        <div class="info-group"><label>Gender:</label><span><?= htmlspecialchars($alumni['gender']) ?></span></div>
        <div class="info-group"><label>Date of Birth:</label><span><?= htmlspecialchars($alumni['dob']) ?></span></div>

        <div class="info-group">
          <label>Email:</label>
          <input type="text" name="email" value="<?= htmlspecialchars($alumni['email']) ?>" required />
        </div>

        <div class="info-group">
          <label>Phone:</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($alumni['phone']) ?>" required />
        </div>

        <div class="info-group">
          <label>Home Address:</label>
          <input type="text" name="home_address" value="<?= htmlspecialchars($alumni['home_address']) ?>" required />
        </div>

        <div class="info-group"><label>Program:</label><span><?= htmlspecialchars($alumni['program']) ?></span></div>
        <div class="info-group"><label>Session:</label><span><?= htmlspecialchars($alumni['session']) ?></span></div>
        <div class="info-group"><label>CGPA:</label><span><?= htmlspecialchars($alumni['cgpa']) ?></span></div>

        <div class="info-group">
          <label>Designation:</label>
          <input type="text" name="designation" value="<?= htmlspecialchars($alumni['designation']) ?>" required />
        </div>

        <div class="info-group">
          <label>Company Name:</label>
          <input type="text" name="company_name" value="<?= htmlspecialchars($alumni['company_name']) ?>" required />
        </div>

        <div class="info-group">
          <label>Job Location:</label>
          <input type="text" name="job_location" value="<?= htmlspecialchars($alumni['job_location']) ?>" required />
        </div>

        <div class="info-group">
          <button type="submit" class="edit-btn">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <!-- Your existing footer content -->
  </footer>

</body>
</html>