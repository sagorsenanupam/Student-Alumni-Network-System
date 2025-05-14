<?php
session_start();
include 'dbconnect.php';

// Predefined font colors to cycle through
$colors = ['#FFD700', '#00FF7F', '#00BFFF', '#FF69B4', '#FF4500', '#ADFF2F', '#DA70D6'];
$colorIndex = 0;

$event_display = '';
$sql = "SELECT event_name, event_description, start_time, end_time, event_creator FROM event WHERE approve = 1 ORDER BY start_time ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $color = $colors[$colorIndex % count($colors)];
        $event_text =  ' || ' . htmlspecialchars($row['event_name']) . ' - ' .
                      htmlspecialchars($row['event_description']) . ' (' .
                      date('M d, H:i', strtotime($row['start_time'])) . ' to ' .
                      date('M d, H:i', strtotime($row['end_time'])) . ') by ' .
                      htmlspecialchars($row['event_creator']) . ' || ';

                    $event_display .= "<span class='event-item' style='color: $color;' title='" . htmlspecialchars($event_text) . "'>$event_text</span>";

        $colorIndex++;
    }
} else {
    $event_display = "<span style='color: white;'>No upcoming events yet.</span>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Registration - Inspiring Excellence</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .scrolling-events {

    overflow: hidden;
    white-space: nowrap;
    padding: 15px 0;
    color: white;
    font-size: 16px;
    font-weight: 500;
    position: relative;
}

.scrolling-wrapper {
    display: inline-block;
    padding-left: 100%;
    animation: scroll-left 30s linear infinite;
}

.scrolling-events:hover .scrolling-wrapper {
    animation-play-state: paused;
}

@keyframes scroll-left {
    0% {
        transform: translateX(0%);
    }
    100% {
        transform: translateX(-100%);
    }
}

.event-item {
    display: inline-block;
    margin-right: 80px;
    cursor: pointer;
}

        
    </style>
</head>
<body>

<!-- Navigation Bar -->
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
        

        <?php if (isset($_SESSION['user_type']) && in_array($_SESSION['user_type'], ['student', 'alumni'])): ?>
    <a href="chat.php">Chat</a>
    <a href="logout.php">Logout</a>
<?php else: ?>
    <a href="alumni_or_student.html">Register</a>
    <a href="login.php">Login</a>
<?php endif; ?>

    </nav>
</header>

<!-- Hero Section -->
<section class="hero">
    <div class="overlay"></div>
    <div class="hero-content">
        <h1>Inspiring Excellence</h1>
        <h2>Alumni</h2>
        <p>We all have an equal responsibility to invest time and energy in shaping their thinking and building their capacities to prepare them for the future.</p>
        <button>See More</button>
    </div>
</section>

<!-- Events Bar -->
<section class="scrolling-events">
    <div class="scrolling-wrapper">
        <?php echo $event_display ?: 'No upcoming events yet.'; ?>
    </div>
</section>

<!-- Steps Section -->
<section class="steps">
    <h1>GET YOURSELF CONNECTED WITH THE EXCELLENCE</h1>
    <div class="steps-container">
        <div class="step-card">
            <div class="step-number">01</div>
            <h2>Log In/Sign Up</h2>
            <p>Login if you already have an account. If you don't have an account, sign up to create one.</p>
        </div>
        <div class="step-card">
            <div class="step-number">02</div>
            <h2>Fill the Forms</h2>
            <p>Fill all the forms provided with precise and credible information.</p>
        </div>
        <div class="step-card">
            <div class="step-number">03</div>
            <h2>Submit Form</h2>
            <p>Click the submit button after filling all the forms with the necessary data.</p>
        </div>
    </div>
</section>

<!-- Requirements Section -->
<section class="requirements">
    <h2>Requirements</h2>
    <p class="req-description">You must satisfy the following in order to be eligible for this application.</p>
    <div class="req-cards">
        <div class="req-card">
            <img src="../assets/logo2.png" alt="Alumni Logo">
            <h3>BRAC University Alumni</h3>
            <p>Applicants must have been officially assigned to, and been a resident of BRAC University during their period of stay in the University.</p>
        </div>
        <div class="req-card">
            <img src="../assets/graduation-hat.png" alt="Graduation Hat">
            <h3>University Student</h3>
            <p>Applicants must be a student of BRAC University.</p>
        </div>
    </div>
</section>

</body>
</html>
