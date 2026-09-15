<?php
// Include the appointments.php file to fetch the appointment data
include('p_dashboardValid.php');
$prescriptionDescriptions = implode("<br>", $prescriptions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="css/p_dashboard.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar Menu -->
        <nav class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="/Comilla_central_medical/p_dashboard.php">Dashboard</a></li>
                <li><a href="/Comilla_central_medical/p_profile.php">Profile</a></li>
                <li><a href="/Comilla_central_medical/appointment.php">Make Appointment</a></li>
                <li><a href="/Comilla_central_medical/p_prescription.php">Prescription</a></li>
                <li><a href="/Comilla_central_medical/p_view_appointment.php">View Appointment</a></li>
                <li><div class="settings-menu">
                    <a href="#settings" id="settings-icon">Settings</a>
                    <div class="dropdown" id="settings-dropdown" style="display: none;">
                        <a href="/Comilla_central_medical/p_change_pass.php">Change Password</a>
                    </div>
                </div></li>
                <li><a href="#help">Help</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>Comilla Central Medical</h1>
                <form action="logout.php" method="POST">
                    <button type="submit" class="logout-button">Logout</button>
                </form>
            </header>
            <header>
                <h1>Your Dashboard</h1>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
            </header>
               
            <!-- Stats Section -->
            <div class="stats">
                <div class="stat-card">
                    Your Upcoming Appointments: <?php echo $upcoming_appointments; ?>
                </div>
                <div class="stat-card">
                    Your Upcoming Appointment Day: <?php echo $formatted_next_appointment; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <button onclick="location.href='/Comilla_central_medical/appointment.php'">Make Appointment</button>
                <button onclick="location.href='/Comilla_central_medical/p_prescription.php'">View prescription</button>
                <button onclick="location.href='/Comilla_central_medical/p_view_appointment.php'">View Appointment</button>
            </div>

            <!-- Health Records -->
            <div class="health-records">
                <h3>Recent Health Records</h3>
                <div class="stat-card">
                <?php
                // Check if there are any prescriptions
                    if (!empty($prescriptions)) {
                        echo $prescriptionDescriptions;  // Display the prescription descriptions
                    } 
                    else {
                        echo "No Health Records";  // If no prescriptions, display this message
                    }
                ?>
            </div>
            </div>

            <!-- Announcements -->
            <div class="announcements">
                <h3>Announcements</h3>
                <div class="announcement-card">Clinic will remain closed on 20 Jan 2025 for maintenance.</div>
                <div class="announcement-card">New Vaccination Program starts from 1 Feb 2025.</div>
            </div>

            <!-- Feedback Section -->
            <div class="feedback-section">
                <h3>Share Your Feedback</h3>
                <textarea placeholder="Write your feedback here..."></textarea>
                <button>Submit Feedback</button>
            </div>
        </div>
    </div>
    <script src="/Comilla_central_medical/p_setting.js"></script>
</body>
</html>