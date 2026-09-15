<?php
include 'db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentId = $_POST['appointmentId'];
    $status = $_POST['status'];

    // Update the appointment status
    $stmt = $conn->prepare("UPDATE appointment SET Status = ? WHERE AppointmentID = ?");
    $stmt->bind_param('si', $status, $appointmentId);

    if ($stmt->execute()) {
        echo "Appointment status updated to $status.";
    } else {
        echo "Failed to update appointment status.";
    }

    $stmt->close();
    $conn->close();

    header('Location: r_dashboard.php');
    exit();
}
?>
