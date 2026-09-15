<?php
// Include the database connection
include 'p_profileValid.php';


// Start session
session_start();

// Get the PatientID from the session
$patient_id = $_SESSION['user_id']; // Make sure the PatientID is stored in the session

// Fetch the appointment details for the patient
$sql = "SELECT a.AppointmentDate, d.DoctorName, a.Status
        FROM Appointment a
        JOIN Doctor d ON a.DoctorID = d.DoctorID
        WHERE a.PatientID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$appointment_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointment - Comilla Central Medical</title>
    <link rel="stylesheet" href="css/p_view_appointment.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar Menu -->
        <nav class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="p_dashboard.php">Dashboard</a></li>
                <li><a href="p_profile.php">Profile</a></li>
                <li><a href="appointment.php">Make Appointment</a></li>
                <li><a href="p_prescription.php">Prescription</a></li>
                <li><a href="p_view_appointment.php">View Appointment</a></li>
                <li><div class="settings-menu">
                    <a href="#settings" id="settings-icon">Settings</a>
                    <div class="dropdown" id="settings-dropdown" style="display: none;">
                        <a href="p_change_pass.php">Change Password</a>
                    </div>
                </div></li>
                <li><a href="#help">Help</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header with Logout button -->
            <header>
                <h1>Comilla Central Medical</h1>
                <form action="logout.php" method="POST">
                    <button type="submit" class="logout-button">Logout</button>
                </form>
            </header>

            <!-- Second Header for the dashboard -->
            <header>
                <h1>Your Appointment</h1>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
            </header>

            <!-- Appointments List -->
            <div class="content">
                <?php
                // Check if any appointment exists
                if ($appointment_result->num_rows > 0) {
                    // Fetch and display each appointment
                    while ($row = $appointment_result->fetch_assoc()) {
                        $appointment_date = new DateTime($row['AppointmentDate']);
                        $formatted_date = $appointment_date->format('F j, Y');
                        $day_of_week = $appointment_date->format('l');
                        echo "<div class='appointment'>";
                        echo "<h2>Date: $formatted_date</h2>";
                        echo "<p><strong>Doctor:</strong> " . htmlspecialchars($row['DoctorName']) . "</p>";
                        echo "<p><strong>Day:</strong> $day_of_week</p>";
                        echo "<div class='status " . htmlspecialchars($row['Status']) . "'>Status: " . htmlspecialchars($row['Status']) . "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No appointment found for this patient.</p>";
                }
                ?>
            </div>
        </div>
    </div>
    <script src="p_setting.js"></script>
</body>
</html>

<?php
// Close the statement and connection
$stmt->close();
$conn->close();
?>
