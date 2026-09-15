<?php
session_start();
include '../db.php';

// Fetch patients who are admitted or discharged
$sql = "SELECT Admission.PatientID, Patient.PatientName, Patient.ContactNumber, Patient.Email, 
        Patient.BloodGroup, Admission.AdmissionDate, Admission.AdmissionStatus 
        FROM Admission 
        JOIN Patient ON Admission.PatientID = Patient.PatientID
        WHERE Admission.AdmissionStatus IN ('Admitted', 'Discharged')";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comilla Central Medical - Patient</title>
    <link rel="stylesheet" href="../css/patient.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar Menu -->
        <nav class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="../r_dashboard.php">Dashboard</a></li>
                <li><a href="../Admission/admission.php">Admission</a></li>
                <li><a href="patient.php">Patients</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
        <form action="../logout.php">
            <header>
                <h1>Comilla Central Medical - Dashboard</h1>
                <button class="logout-button">Logout</button>
            </header>
            </form>
            <div class="content">
                <h2>Patient Management</h2>
                <div class="patient-management">
                    <h3>Search Patient</h3>
                    <form class="search-form">
                        <label for="patient-id">Patient ID</label>
                        <input type="text" id="patient-id" placeholder="Enter Patient ID" onkeyup="fetchPatientData()">

                        <label for="patient-name">Patient Name</label>
                        <input type="text" id="patient-name" placeholder="Enter Patient Name" onkeyup="fetchPatientData()">

                        <label for="patient-mobile">Mobile Number</label>
                        <input type="text" id="patient-mobile" placeholder="Enter Mobile Number" onkeyup="fetchPatientData()">
                    </form>
                </div>

                <div class="patient-table">
                    <h3>Patient List</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Patient ID</th>
                                <th>Name</th>
                                <th>Mobile Number</th>
                                <th>Email</th>
                                <th>Blood Group</th>
                                <th>Admission Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="patient-data">
                            <?php
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
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<script src="js/patient.js"></script>
</body>
</html>
