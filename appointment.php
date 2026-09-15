<?php
session_start();
include 'p_profileValid.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientID = $_POST['patient-id'] ?? null;
    $patientName = $_POST['fullname'];
    $contactNumber = $_POST['phone'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $specialty = $_POST['specialty'];
    $doctorID = $_POST['doctor'];
    $appointmentDate = $_POST['date']; // Ensure this is passed from the form

    try {
        if ($patientID) {
            // Update existing patient
            $updatePatientQuery = "UPDATE Patient SET PatientName = ?, Email = ?, Gender = ?, DateOfBirth = ? WHERE PatientID = ?";
            $stmt = $conn->prepare($updatePatientQuery);
            $stmt->bind_param("ssssi", $patientName, $email, $gender, $dob, $patientID);
            $stmt->execute();
        } else {
            // Insert new patient
            $Password = '12345678'; // Default password
            $insertPatientQuery = "INSERT INTO Patient (PatientName, ContactNumber, Email, Gender, DateOfBirth, Address, BloodGroup, Password) 
                                   VALUES (?, ?, ?, ?, ?, NULL, NULL, ?)";
            $stmt = $conn->prepare($insertPatientQuery);
            $stmt->bind_param("ssssss", $patientName, $contactNumber, $email, $gender, $dob, $Password);
            $stmt->execute();
            $patientID = $stmt->insert_id;
        }

        // Insert appointment details
        $appointmentQuery = "INSERT INTO appointment (PatientID, DoctorID, AppointmentDate, Status) 
                             VALUES (?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($appointmentQuery);
        $stmt->bind_param("iis", $patientID, $doctorID, $appointmentDate);
        $stmt->execute();

        $successMessage = "Patient appointment made successfully!";
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}
  
// Fetch specialties dynamically
$specialtyQuery = "SELECT DISTINCT Specialization FROM Doctor";
$specialtyResult = $conn->query($specialtyQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Appointment</title>
    <link rel="stylesheet" href="css/appointment_style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <header>
                <h1>Comilla Central Medical</h1>
                <form action="logout.php" method="POST">
                    <button type="submit" class="logout-button">Logout</button>
                </form>
            </header>

            <?php if (isset($successMessage)) : ?>
                <p class="successs"><?= $successMessage ?></p>
            <?php elseif (isset($errorMessage)) : ?>
                <p class="errorr"><?= $errorMessage ?></p>
            <?php endif; ?>
            
            <!-- Appointment Form -->
            <form class="appointment-form"  method="post">
                <h1>Make Appointment</h1>
                <div class="form-group">
                     <input type="hidden" id="patient-id" name="patient-id">
                </div>
                <div class="form-group">
                    <label for="mobile">Mobile:</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter your mobile number" required>
                </div>
                
                <div class="form-group">
                    <label for="fullname">Patient's Full Name:</label>
                    <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required>
                </div>
                
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" name="dob" id="dob" required>
                </div>
                
                <div class="form-group">
                    <label>Sex:</label>
                    <div class="radio-group">
                    <select id="gender" name="gender" required>
                        <option value="">Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    </div>
                </div>
                
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                
                <div class="form-group">
                    <label for="specialty">Specialty for Consultation:</label>
                    <select id="specialty" name="specialty" required>
                        <option value="">Choose specialty</option>
                        <?php while ($specialty = $specialtyResult->fetch_assoc()) : ?>
                            <option value="<?= $specialty['Specialization'] ?>"><?= $specialty['Specialization'] ?></option>
                        <?php endwhile; ?>
                        
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="doctor">Doctor:</label>
                    <select id="doctor" name="doctor" required>
                        <option value="">Select Consultant</option>
                        
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Preferred Day and Date for Appointment:</label>
                    <select id="appointment-date" name="appointment-date" required>
                        <option value="">Day</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                    </select>
                    <label for="Date">Date :</label>
                    <input type="date" name="date" id="date" required>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn-request">Request</button>
                    <button type="reset" class="btn-reset">Reset</button>
                </div>
            </form>
        </div>
    </div>
    <script src="appointment.js"></script>
</body>
</html>
