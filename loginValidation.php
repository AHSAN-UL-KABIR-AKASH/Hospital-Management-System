<?php
include 'db.php'; // Ensure this includes a secure connection setup
session_start();

// Validate form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['user_type'])) {
    header('Location: loginpage.php');
    exit;
}

// Sanitize inputs
$user_type = mysqli_real_escape_string($conn, trim($_POST['user_type']));
$password = mysqli_real_escape_string($conn, trim($_POST['password']));
$username_or_contact = '';

if ($user_type === 'patient') {
    $username_or_contact = mysqli_real_escape_string($conn, trim($_POST['contact_number']));
} else {
    $username_or_contact = mysqli_real_escape_string($conn, trim($_POST['username']));
}

// SQL query and validation
$sql = '';
$redirect_page = '';

if ($user_type === 'patient') {
    $sql = "SELECT * FROM Patient WHERE ContactNumber = '$username_or_contact' AND Password = '$password' LIMIT 1";
    $redirect_page = 'p_dashboard.php';
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $_SESSION['user_id'] =$row['PatientID'];
    $_SESSION['user_name'] =$row['PatientName'];
} elseif ($user_type === 'receptionist') {
    $sql = "SELECT * FROM Receptionist WHERE RecNumber = '$username_or_contact' AND RecPassword = '$password' LIMIT 1";
    $redirect_page = 'r_dashboard.php';
} elseif ($user_type === 'manager') {
    $sql = "SELECT * FROM Manager WHERE UserName = '$username_or_contact' AND Password = '$password' LIMIT 1";
    $redirect_page = 'managerDashboard.php';
}

// Execute query
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $_SESSION['userType'] = $user_type;
    header("Location: $redirect_page");
} else {
    // Redirect back with error
    $_SESSION['error'] = 'Invalid credentials or user type.';
    header('Location: loginpage.php');
}

mysqli_close($conn);
?>
