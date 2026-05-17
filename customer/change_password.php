<?php
session_start();
include("db.php"); // your database connection file

if (isset($_POST['update'])) {
    $c_email = $_SESSION['customer_email'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $c_n_password = $_POST['c_n_password'];

    // Validation
    if (empty($old_password) || empty($new_password) || empty($c_n_password)) {
        echo "<script>alert('All fields are required!');</script>";
        exit();
    }

    if ($new_password !== $c_n_password) {
        echo "<script>alert('New Password and Confirm Password do not match!');</script>";
        exit();
    }

    // Fetch current user data
    $stmt = $con->prepare("SELECT customer_pass FROM customers WHERE customer_email = ?");
    $stmt->bind_param("s", $c_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('User not found.');</script>";
        exit();
    }

    $row = $result->fetch_assoc();
    $hashed_password = $row['customer_pass'];

    // Verify old password
    if (!password_verify($old_password, $hashed_password)) {
        echo "<script>alert('Your Current Password is not valid. Try again.');</script>";
        exit();
    }

    // Hash new password
    $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update password
    $update_stmt = $con->prepare("UPDATE customers SET customer_pass = ? WHERE customer_email = ?");
    $update_stmt->bind_param("ss", $new_hashed_password, $c_email);
    $update_stmt->execute();

    echo "<script>alert('Your Password has been changed successfully!');</script>";
    echo "<script>window.open('my_account.php?my_order','_self');</script>";
}
?>
