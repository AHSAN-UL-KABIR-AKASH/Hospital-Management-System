<?php
// Include database connection file
include 'p_profileValid.php';

// Start session to access user info
session_start();

if (!isset($_SESSION['user_id'])) {
    // If the session is not set, redirect to the login page
    header("Location: loginValidation.php");
    exit();
}

// Initialize variables to store messages
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate new password
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = 'All fields are required.';
    } 
    elseif ($new_password !== $confirm_password) {
        $error_message = 'New password and confirmation do not match.';
    } 
    else {
        // Fetch current password from the database
        $patient_id = $_SESSION['user_id'];
        $query = "SELECT password FROM patient WHERE PatientID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $patient_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($stored_password);
        $stmt->fetch();

        // Verify the current password
        if ($current_password == $stored_password) {
            // Hash the new password
            $new_password_hashed = $new_password;

            // Update the password in the database
            $update_query = "UPDATE patient SET password = ? WHERE PatientID = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param('si', $new_password_hashed, $patient_id);
            if ($update_stmt->execute()) {
                $success_message = 'Password changed successfully!';
            } 
            else {
                $error_message = 'Failed to update password. Please try again.';
            }
        } 
        else {
            $error_message = 'Current password is incorrect.';
        }
    }
}
?>