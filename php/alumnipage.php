<?php
session_start();
include 'dbconnect.php';

// Capture filters from GET parameters
$gender = isset($_GET['gender']) ? $_GET['gender'] : '';
$program = isset($_GET['program']) ? $_GET['program'] : '';

// Prepare SQL query with optional filters
$query = "SELECT name, designation, company_name, job_location FROM Alumni WHERE approve = 1";
$params = [];

if ($gender !== '') {
    $query .= " AND gender = ?";
    $params[] = $gender;
}

if ($program !== '') {
    $query .= " AND program = ?";
    $params[] = $program;
}

// Prepare statement and bind parameters
$stmt = $conn->prepare($query);
if ($params) {
    $types = str_repeat('s', count($params)); // assuming all string parameters
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$alumni = [];
while ($row = $result->fetch_assoc()) {
    $alumni[] = $row;
}
$stmt->close();

// Get unique values for gender and program for the filter dropdowns
$genders = $conn->query("SELECT DISTINCT gender FROM Alumni WHERE gender IS NOT NULL AND gender != ''");
$programs = $conn->query("SELECT DISTINCT program FROM Alumni WHERE program IS NOT NULL AND program != ''");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approved Alumni</title>
    <style>
       /* === Reset and Base === */
/* === Reset and Base === */
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f9f9f9;
    padding-top: 80px; /* Space for fixed navbar */
    overflow-x: hidden;
}

/* === Navbar === */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    background: #ebebeb;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    flex-wrap: wrap;
}

.navbar .logo img {
    width: 60px;
    height: auto;
}

.nav-links {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 15px;
}

.nav-links a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    font-size: 15px;
    padding: 8px 10px;
    transition: color 0.3s;
    white-space: nowrap;
}

.nav-links a:hover {
    color: #4299e1;
}

/* === Filter Panel === */
.filter-panel {
    width: 220px;
    float: left;
    padding: 20px;
    background-color: #f0f0f0;
    min-height: calc(100vh - 80px);
    box-sizing: border-box;
    border-right: 1px solid #ddd;
    position: relative;
}

.filter-panel label {
    font-weight: bold;
    display: block;
    margin: 10px 0 5px;
}

.filter-panel select,
.filter-panel input[type="submit"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.filter-panel input[type="submit"] {
    background-color: #007bff;
    color: white;
    cursor: pointer;
}

.filter-panel input[type="submit"]:hover {
    background-color: #0056b3;
}

/* === Main Content === */
.main-content {
    margin-left: 240px;
    padding: 30px;
    overflow-x: hidden;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 30px;
}

/* === Alumni Card === */
.card {
    background-color: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.2s;
}

.card:hover {
    transform: scale(1.03);
}

.card img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 15px;
}

.card h3 {
    margin: 10px 0 5px;
    font-size: 18px;
    color: #333;
}

.card p {
    margin: 4px 0;
    font-size: 14px;
    color: #555;
}

/* === Footer === */
.footer {
    background-color: #2c3e50;
    color: white;
    padding: 40px 30px 20px;
    clear: both;
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

.social-links a {
    display: flex;
    align-items: center;
    gap: 8px;
}

.copyright {
    text-align: center;
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #34495e;
    color: #bdc3c7;
    font-size: 14px;
}


    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <header class="navbar">
    <div class="logo">
        <a href="index.html">
            <img src="../assets/logo2.png" alt="Logo">
        </a>
    </div>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="about.html">About</a>
        <a href="http://localhost/student_alumni_network_system/php/event.php">Events</a>
        <a href="alumni_or_student.html">Register</a>
        <a href="http://localhost/student_alumni_network_system/php/login.php">Logout</a>
    </nav>
</header>

<div class="filter-panel">
    <form method="get" action="alumnipage.php">
        <label for="gender">Gender</label><br>
        <select name="gender" id="gender">
            <option value="">All</option>
            <?php while ($row = $genders->fetch_assoc()): ?>
                <option value="<?= $row['gender'] ?>" <?= $gender === $row['gender'] ? 'selected' : '' ?>>
                    <?= $row['gender'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="program">Program</label><br>
        <select name="program" id="program">
            <option value="">All</option>
            <?php while ($row = $programs->fetch_assoc()): ?>
                <option value="<?= $row['program'] ?>" <?= $program === $row['program'] ? 'selected' : '' ?>>
                    <?= $row['program'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <input type="submit" value="Filter">
    </form>
</div>

<div class="main-content">
    <div class="grid">
        <?php if (count($alumni) > 0): ?>
            <?php foreach ($alumni as $alum): ?>
                <div class="card">
                    <img src="../assets/alumni.png" alt="Profile Picture">
                    <h3><?= htmlspecialchars($alum['name']) ?></h3>
                    <p><?= htmlspecialchars($alum['designation']) ?></p>
                    <p><?= htmlspecialchars($alum['company_name']) ?></p>
                    <p><?= htmlspecialchars($alum['job_location']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No alumni found with the selected filters.</p>
        <?php endif; ?>
    </div>
</div>

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
