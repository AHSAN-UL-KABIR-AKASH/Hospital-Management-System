<?php
// appointments.php

session_start();
if($_SESSION['userType'] != "patient")
{
    header("Location: loginPage.php");
    exit();
}


// Now you can access the session data
$patient_id = $_SESSION['user_id'];

// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "Comilla_central_medical"; // Replace with your database name

// Create a database connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch prescriptions for the patient
$sql = "SELECT  p.PrescriptionDescription
        FROM Prescription p
        JOIN Doctor d ON p.DoctorID = d.DoctorID
        WHERE p.PatientID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$prescriptions = [];
while ($row = $result->fetch_assoc()) {
    $prescriptions[] = $row['PrescriptionDescription'];
}

// Query 1: Count of upcoming appointments
$sql_count = "SELECT COUNT(*) AS upcoming_appointments 
              FROM Appointment 
              WHERE PatientID = ? AND AppointmentDate > NOW()";

// Query 2: Nearest upcoming appointment date
$sql_next = "SELECT IFNULL(MIN(AppointmentDate), 0) AS next_appointment
             FROM Appointment
             WHERE PatientID = ? AND AppointmentDate > CURDATE()";

// Prepare and execute the query for upcoming appointments count
$stmt_count = $conn->prepare($sql_count);
if (!$stmt_count) {
    die("SQL statement preparation failed (count): " . $conn->error);
}
$stmt_count->bind_param("i", $patient_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$upcoming_appointments = 0;
if ($row = $result_count->fetch_assoc()) {
    $upcoming_appointments = $row['upcoming_appointments'];
}
$stmt_count->close();

// Prepare and execute the query for nearest upcoming appointment
$stmt_next = $conn->prepare($sql_next);
if (!$stmt_next) {
    die("SQL statement preparation failed (next): " . $conn->error);
}
$stmt_next->bind_param("i", $patient_id);
$stmt_next->execute();
$result_next = $stmt_next->get_result();
$next_appointment = "0";
if ($row = $result_next->fetch_assoc()) {
    $next_appointment = $row['next_appointment'];
}
$stmt_next->close();

// Close the database connection
$conn->close();

// Format the next appointment date
if ($next_appointment !== "0") {
    $formatted_next_appointment = date("d M Y", strtotime($next_appointment));
} else {
    $formatted_next_appointment = "No ";
}
?>
