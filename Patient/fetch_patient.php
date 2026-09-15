<?php
session_start();
include '../db.php';


// Get the search parameters
$patientId = isset($_POST['patientId']) ? $_POST['patientId'] : '';
$patientName = isset($_POST['patientName']) ? $_POST['patientName'] : '';
$patientMobile = isset($_POST['patientMobile']) ? $_POST['patientMobile'] : '';

// Build the query
$sql = "SELECT Admission.PatientID, Patient.PatientName, Patient.ContactNumber, Patient.Email, 
        Patient.BloodGroup, Admission.AdmissionDate, Admission.AdmissionStatus 
        FROM Admission 
        JOIN Patient ON Admission.PatientID = Patient.PatientID
        WHERE Admission.AdmissionStatus IN ('Admitted', 'Discharged')";

if (!empty($patientId)) {
    $sql .= " AND Admission.PatientID LIKE '%" . $conn->real_escape_string($patientId) . "%'";
}
if (!empty($patientName)) {
    $sql .= " AND Patient.PatientName LIKE '%" . $conn->real_escape_string($patientName) . "%'";
}
if (!empty($patientMobile)) {
    $sql .= " AND Patient.ContactNumber LIKE '%" . $conn->real_escape_string($patientMobile) . "%'";
}

$result = mysqli_query($conn, $sql);

// Generate the HTML for the table rows
if ($result->num_rows > 0) {
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo "<tr>
                <td>{$row['PatientID']}</td>
                <td>{$row['PatientName']}</td>
                <td>{$row['ContactNumber']}</td>
                <td>{$row['Email']}</td>
                <td>{$row['BloodGroup']}</td>
                <td>{$row['AdmissionDate']}</td>
                <td>{$row['AdmissionStatus']}</td>
                <td>
                    <button class='discharge-btn' onclick='dischargePatient({$row['PatientID']})'>Discharge</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No patients found</td></tr>";
}

$conn->close();
?>
