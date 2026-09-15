<?php
// Include the database connection
include 'p_profileValid.php';

// Start session
session_start();

if($_SESSION['userType'] != "patient")
{
    header("Location: loginPage.php");
    exit();
}

// Get the patient ID from the session
$patient_id = $_SESSION['user_id'];

// Fetch prescriptions for the patient
$sql = "SELECT p.Date, p.PrescriptionDescription, d.DoctorName 
        FROM Prescription p
        JOIN Doctor d ON p.DoctorID = d.DoctorID
        WHERE p.PatientID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescriptions - Comilla Central Medical</title>
    <link rel="stylesheet" href="css/p_prescription.css">
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
                <li>
                    <div class="settings-menu">
                        <a href="#settings" id="settings-icon">Settings</a>
                        <div class="dropdown" id="settings-dropdown" style="display: none;">
                            <a href="p_change_pass.php">Change Password</a>
                        </div>
                    </div>
                </li>
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

            <!-- Prescription Page Header -->
            <header>
                <h1>Your Prescription</h1>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
            </header>

            <div class="content">
                <!-- Prescriptions List -->
                <?php
                // Check if there are any prescriptions
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <div class="prescription">
                            <h2>Date: <?php echo htmlspecialchars((new DateTime($row['Date']))->format('F j, Y')); ?></h2>
                            <p><strong>Prescribed by:</strong> <?php echo htmlspecialchars($row['DoctorName']); ?></p>
                            <div class="medications">
                                <strong>Prescription Details:</strong>
                                <p><?php echo nl2br(htmlspecialchars($row['PrescriptionDescription'])); ?></p>
                            </div>
                            <a href="pdf.php" class="download-button">Download PDF</a>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No prescriptions found for this patient.</p>";
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
