<?php
// p_profile.php

session_start();


// Include the database connection
include 'p_profileValid.php';

// Get patient details
$patient_id = $_SESSION['user_id'];

// Get patient details from the database
$sql = "SELECT * FROM Patient WHERE PatientID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - Comilla Central Medical</title>
    <link rel="stylesheet" href="css/p_profile.css">
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
                <!-- Profile Form -->
                <form class="appointment-form" action="p_update.php" method="post" onsubmit="return validateProfileForm()">
                    <h1>Your Profile</h1>
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($patient['PatientName']); ?>" required>
                        <small class="error" id="name_error"></small>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($patient['DateOfBirth']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="Male" <?php echo ($patient['Gender'] == 'Male' ? 'selected' : ''); ?>>Male</option>
                            <option value="Female" <?php echo ($patient['Gender'] == 'Female' ? 'selected' : ''); ?>>Female</option>
                            <option value="Other" <?php echo ($patient['Gender'] == 'Other' ? 'selected' : ''); ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($patient['ContactNumber']); ?>" readonly>
                        <small class="error" id="contact_error"></small>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($patient['Email']); ?>" required>
                        <small class="error" id="email_error"></small>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($patient['Address']); ?>" required>
                        <small class="error" id="address_error"></small>
                    </div>
                    <div class="form-group">
                        <label for="blood_group">Blood Group</label>
                        <select id="blood_group" name="blood_group" required>
                            <option value="A+" <?php echo ($patient['BloodGroup'] == 'A+' ? 'selected' : ''); ?>>A+</option>
                            <option value="A-" <?php echo ($patient['BloodGroup'] == 'A-' ? 'selected' : ''); ?>>A-</option>
                            <option value="B+" <?php echo ($patient['BloodGroup'] == 'B+' ? 'selected' : ''); ?>>B+</option>
                            <option value="B-" <?php echo ($patient['BloodGroup'] == 'B-' ? 'selected' : ''); ?>>B-</option>
                            <option value="O+" <?php echo ($patient['BloodGroup'] == 'O+' ? 'selected' : ''); ?>>O+</option>
                            <option value="O-" <?php echo ($patient['BloodGroup'] == 'O-' ? 'selected' : ''); ?>>O-</option>
                            <option value="AB+" <?php echo ($patient['BloodGroup'] == 'AB+' ? 'selected' : ''); ?>>AB+</option>
                            <option value="AB-" <?php echo ($patient['BloodGroup'] == 'AB-' ? 'selected' : ''); ?>>AB-</option>
                        </select>
                    </div>
                    <div class="btn-group">
                        <button type="submit">Update Profile</button>
                    </div>
                </form> 
            </div>
        </div>
   
    <script src="p_update.js"></script>
</body>
</html>
