<?php
session_start();
include '../db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientID = $_POST['patient-id'] ?? null;
    $patientName = $_POST['name'];
    $contactNumber = $_POST['phone'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $bloodGroup = $_POST['blood-group'];
    $specialty = $_POST['specialty'];
    $doctorID = $_POST['doctor'];
    $roomID = $_POST['room'];
    $admissionDate = $_POST['admission-date'];

    try {
        if ($patientID) {
            // Update existing patient
            $updatePatientQuery = "UPDATE Patient SET PatientName = ?, Email = ?, Gender = ?, DateOfBirth = ?, Address = ?, BloodGroup = ? WHERE PatientID = ?";
            $stmt = $conn->prepare($updatePatientQuery);
            $stmt->bind_param("ssssssi", $patientName, $email, $gender, $dob, $address, $bloodGroup, $patientID);
            $stmt->execute();
        } else {
            // Insert new patient
            $insertPatientQuery = "INSERT INTO Patient (PatientName, ContactNumber, Email, Gender, DateOfBirth, Address, BloodGroup) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertPatientQuery);
            $stmt->bind_param("sssssss", $patientName, $contactNumber, $email, $gender, $dob, $address, $bloodGroup);
            $stmt->execute();
            $patientID = $stmt->insert_id;
        }

        // Insert admission details
        $patientBill = 10000.00; // Example bill amount
        $admissionQuery = "INSERT INTO Admission (PatientID, RoomID, DoctorID, AdmissionDate, AdmissionStatus, PatientBill) VALUES (?, ?, ?, ?, 'Admitted', ?)";
        $stmt = $conn->prepare($admissionQuery);
        $stmt->bind_param("iiisd", $patientID, $roomID, $doctorID, $admissionDate, $patientBill);
        $stmt->execute();

        $successMessage = "Patient admitted successfully!";
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

// Fetch specialties dynamically
$specialtyQuery = "SELECT DISTINCT Specialization FROM Doctor";
$specialtyResult = $conn->query($specialtyQuery);

// Fetch available rooms dynamically
$roomQuery = "SELECT RoomID, RoomNumber FROM Room";
$roomResult = $conn->query($roomQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comilla Central Medical - Patient Admission</title>
    <link rel="stylesheet" href="../css/admission.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="../r_dashboard.php">Dashboard</a></li>
                <li><a href="../Admission/admission.php">Admission</a></li>
                <li><a href="../Patient/patient.php">Patients</a></li>
            </ul>
        </nav>

        <div class="main-content">
            <form action="../logout.php">
            <header>
                <h1>Comilla Central Medical - Dashboard</h1>
                <button class="logout-button">Logout</button>
            </header>
            </form>
            <div class="content">
                <h2>Patient Admission</h2>
                
                <?php if (isset($successMessage)) : ?>
                    <p class="success-message"><?= $successMessage ?></p>
                <?php elseif (isset($errorMessage)) : ?>
                    <p class="error-message"><?= $errorMessage ?></p>
                <?php endif; ?>

                <form class="admission-form" method="POST">
                    <table><td>
                    <input type="hidden" id="patient-id" name="patient-id">

                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" required>

                    <label for="name">Patient Name:</label>
                    <input type="text" id="name" name="name" required>

                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" required>

                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="blood-group">Blood Group:</label>
                    <select id="blood-group" name="blood-group" required>
                        <option value="">Select</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select></td><td>

                    <label for="address">Address:</label>
                    <textarea id="address" name="address" rows="3" required></textarea>

                    <label for="specialty">Specialty for Consultation:</label>
                    <select id="specialty" name="specialty" required>
                        <option value="">Select</option>
                        <?php while ($specialty = $specialtyResult->fetch_assoc()) : ?>
                            <option value="<?= $specialty['Specialization'] ?>"><?= $specialty['Specialization'] ?></option>
                        <?php endwhile; ?>
                    </select>

                    <label for="doctor">Doctor:</label>
                    <select id="doctor" name="doctor" required>
                        <option value="">Select the Specialty First</option>
                    </select>

                    <label for="room">Room Number:</label>
                    <select id="room" name="room" required>
                        <option value="">Select</option>
                        <?php while ($room = $roomResult->fetch_assoc()) : ?>
                            <option value="<?= $room['RoomID'] ?>">Room <?= $room['RoomNumber'] ?></option>
                        <?php endwhile; ?>
                    </select>

                    <label for="admission-date">Admission Date:</label>
                    <input type="date" id="admission-date" name="admission-date" required>
                    </td></table>
                    <button type="submit" class="submit-button">Admit Patient</button>
                    
                </form>
            </div>
        </div>
    </div>

<script src="js/admission.js"></script>
    
</body>
</html>
