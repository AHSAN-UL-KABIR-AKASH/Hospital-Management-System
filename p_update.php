<?php
// p_profileValid.php

// Include the database connection
include 'p_profileValid.php';

// Start the session
session_start();
// Check if the request is an AJAX POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// Get the form data
$patient_id = $_SESSION['user_id'];
$full_name = $_POST['full_name'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$contact_number = $_POST['contact_number'];
$email = $_POST['email'];
$address = $_POST['address'];
$blood_group = $_POST['blood_group'];

// Prepare the SQL update statement
$sql = "UPDATE Patient SET PatientName = ?, DateOfBirth = ?, Gender = ?, ContactNumber = ?, Email = ?, Address = ?, BloodGroup = ? WHERE PatientID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssi", $full_name, $dob, $gender, $contact_number, $email, $address, $blood_group, $patient_id);

// Execute the update statement
if ($stmt->execute()) {
    // Return success message as JSON
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
}
else {
    // Return error message as JSON
    echo json_encode(['status' => 'error', 'message' => 'Error updating profile: ' . $stmt->error]);
}
    // Close the statement
    $stmt->close();
} 
else {
     echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
// Close the statement
exit();
?>
