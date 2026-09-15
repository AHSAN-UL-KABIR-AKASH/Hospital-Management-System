<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'];

    $query = "SELECT * FROM Patient WHERE ContactNumber = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
        echo json_encode(['exists' => true] + $patient);
    } else {
        echo json_encode(['exists' => false]);
    }
}
?>
