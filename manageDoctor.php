<?php
session_start();
include 'db.php';

// Initialize variables for the form inputs
$doctorName = $specialization = $fee = $contactNumber = $email = $doctorId = '';

// Handle the edit operation
if (isset($_GET['edit'])) {
    $doctorId = $_GET['edit'];
    // Fetch doctor details
    $stmt = $conn->prepare("SELECT * FROM Doctor WHERE DoctorID = ?");
    $stmt->bind_param("i", $doctorId);
    $stmt->execute();
    $result = $stmt->get_result();
    $doctor = $result->fetch_assoc();

    // Assign values to the form fields
    if ($doctor) {
        $doctorName = $doctor['DoctorName'];
        $specialization = $doctor['Specialization'];
        $fee = $doctor['Fees'];
        $contactNumber = $doctor['ContactNumber'];
        $email = $doctor['DoctorEmail'];
    }
    $stmt->close();
}

// Handle form submission for update or insert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entity = $_POST['entity'] ?? '';
    $action = $_POST['action'] ?? '';

    if ($entity === 'doctor') {
        if ($action === 'update') {
            // Update the doctor information
            $doctorName = $_POST['doctor_name'];
            $specialization = $_POST['specialization'];
            $fee = $_POST['fee'];
            $contactNumber = $_POST['contact_number'];
            $email = $_POST['email'];
            $doctorId = $_POST['doctor_id'];

            if (!empty($doctorName) && !empty($specialization) && !empty($fee) && !empty($contactNumber) && !empty($email)) {
                $stmt = $conn->prepare("UPDATE Doctor SET DoctorName=?, Specialization=?, Fees=?, ContactNumber=?, DoctorEmail=? WHERE DoctorID=?");
                $stmt->bind_param("ssdssi", $doctorName, $specialization, $fee, $contactNumber, $email, $doctorId);
                $stmt->execute();
                $stmt->close();
            }
        } elseif ($action === 'insert') {
            // Insert new doctor into the database
            $doctorName = $_POST['doctor_name'];
            $specialization = $_POST['specialization'];
            $fee = $_POST['fee'];
            $contactNumber = $_POST['contact_number'];
            $email = $_POST['email'];

            if (!empty($doctorName) && !empty($specialization) && !empty($fee) && !empty($contactNumber) && !empty($email)) {
                $stmt = $conn->prepare("INSERT INTO Doctor (DoctorName, Specialization, Fees, ContactNumber, DoctorEmail) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdss", $doctorName, $specialization, $fee, $contactNumber, $email);
                $stmt->execute();
                $stmt->close();
            }
        } elseif ($action === 'delete') {
            $doctorId = $_POST['doctor_id'];
            if (!empty($doctorId)) {
                $stmt = $conn->prepare("DELETE FROM Doctor WHERE DoctorID = ?");
                $stmt->bind_param("i", $doctorId);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}

// Fetch all doctors for display
$doctor = $conn->query("SELECT * FROM Doctor");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors</title>
    <link rel="stylesheet" href="css/manageDoctor.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="managerDashboard.php">Dashboard</a></li>
                <li><a href="manager.php">Manage Rooms</a></li>
                <li><a href="manageDoctor.php">Manage Doctors</a></li>
                <li><a href="manageTests.php">Manage Tests</a></li>
                <li><a href="manageReceptionist.php">Manage Receptionist</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <form action="logout.php">
                <header>
                    <h1>Comilla Central Medical</h1>
                    <button class="logout-button">Logout</button>
                </header>
            </form>

            <div class="content">
                <h2>Add or Update Doctor Information</h2>
                <div class="input-container">
                    <form method="POST">
                        <input type="hidden" name="entity" value="doctor">
                        <!-- Determine the action (update or insert) -->
                        <input type="hidden" name="action" value="<?= isset($doctorId) ? 'update' : 'insert' ?>">
                        <?php if (isset($doctorId)) { ?>
                            <input type="hidden" name="doctor_id" value="<?= $doctorId ?>">
                        <?php } ?>

                        <input type="text" name="doctor_name" placeholder="Doctor Name" value="<?= $doctorName ?? '' ?>" required>
                        <input type="text" name="specialization" placeholder="Specialization" value="<?= $specialization ?? '' ?>" required>
                        <input type="number" name="fee" placeholder="Visit Fee" step="0.01" value="<?= $fee ?? '' ?>" required>
                        <input type="text" name="contact_number" placeholder="Contact Number" value="<?= $contactNumber ?? '' ?>" required>
                        <input type="email" name="email" placeholder="Email Address" value="<?= $email ?? '' ?>" required>

                        <button type="submit" class="update-doctor-button"><?= isset($doctorId) ? 'Update Doctor' : 'Add Doctor' ?></button>
                        <?php if (!isset($doctorId)) { ?>
                            <a href="manageDoctor.php" class="add-new-doctor-button">Add New Doctor</a>
                        <?php } ?>
                    </form>
                </div>

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Fee</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($row = $doctor->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['DoctorID'] ?></td>
                            <td><?= $row['DoctorName'] ?></td>
                            <td><?= $row['Specialization'] ?></td>
                            <td><?= $row['Fees'] ?></td>
                            <td><?= $row['ContactNumber'] ?></td>
                            <td><?= $row['DoctorEmail'] ?></td>
                            <td>
                                <a href="manageDoctor.php?edit=<?= $row['DoctorID'] ?>">Edit</a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="entity" value="doctor">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="doctor_id" value="<?= $row['DoctorID'] ?>">
                                    <button type="submit" class="delete-button">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
