<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $specialty = $_POST['specialty'];

    if (!empty($specialty)) {
        $doctorQuery = "SELECT DoctorID, DoctorName FROM Doctor WHERE Specialization = ? AND DoctorAvailable = TRUE";
        $stmt = $conn->prepare($doctorQuery);
        $stmt->bind_param("s", $specialty);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($doctor = $result->fetch_assoc()) {
                echo '<option value="' . $doctor['DoctorID'] . '">' . $doctor['DoctorName'] . '</option>';
            }
        } else {
            echo '<option value="">No doctors available</option>';
        }
    } else {
        echo '<option value="">Invalid specialty selected</option>';
    }
}
?>
