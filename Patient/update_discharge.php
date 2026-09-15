<?php
session_start();
include '../db.php';

$patientId = $_POST['patientId'];

if (!empty($patientId)) {
    $sql = "UPDATE Admission SET AdmissionStatus = 'Discharged' WHERE PatientID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patientId);

    if ($stmt->execute()) {
        echo "Patient discharged successfully.";
    } else {
        echo "Error discharging patient.";
    }
    $stmt->close();
}

$conn->close();
?>
