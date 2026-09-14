<?php
session_start();

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'comilla_central_medical';

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $recName = $_POST['rec_name'];
        $recAdress = $_POST['rec_address'];
        $recNumber = $_POST['rec_number'];
        $recPassword = $_POST['rec_password'];

        if (!empty($recName) && !empty($recAdress) && !empty($recNumber) && !empty($recPassword)) {
            $stmt = $conn->prepare("INSERT INTO receptionist (RecName, RecAdress, RecNumber, RecPassword) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $recName, $recAdress, $recNumber, $recPassword);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($action === 'delete') {
        $recId = $_POST['rec_id'];
        if (!empty($recId)) {
            $stmt = $conn->prepare("DELETE FROM receptionist WHERE RecID = ?");
            $stmt->bind_param("i", $recId);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($action === 'update') {
        $recId = $_POST['rec_id'];
        $recName = $_POST['rec_name'];
        $recAdress = $_POST['rec_address'];
        $recNumber = $_POST['rec_number'];
        $recPassword = $_POST['rec_password'];

        if (!empty($recId) && !empty($recName) && !empty($recAdress) && !empty($recNumber) && !empty($recPassword)) {
            $stmt = $conn->prepare("UPDATE receptionist SET RecName = ?, RecAdress = ?, RecNumber = ?, RecPassword = ? WHERE RecID = ?");
            $stmt->bind_param("ssssi", $recName, $recAdress, $recNumber, $recPassword, $recId);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Fetch data for display
$receptionists = $conn->query("SELECT * FROM receptionist");
?>

<!DOCTYPE html>
<html lang="en">
<!-- <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Receptionists</title>
    <link rel="stylesheet" href="css/ManageReception.css">
</head> -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Receptionist</title>
    <link rel="stylesheet" href="sempleMenuBar/sempleMenuBar.css">
    <link rel="stylesheet" href="css/ManageReception.css">
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
                <h2 style="text-align: center;">Add or Edit Receptionist</h2>
                <div class="input-container">
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        <input type="text" name="rec_name" placeholder="Receptionist Name" required>
                        <input type="text" name="rec_address" placeholder="Address" required>
                        <input type="text" name="rec_number" placeholder="Contact Number" required>
                        <input type="password" name="rec_password" placeholder="Password" required>
                        <button type="submit" class="add-receptionist-button">Add Receptionist</button>
                    </form>
                </div>

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Contact</th>
                        <th>Password</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($row = $receptionists->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['RecID'] ?></td>
                            <td><?= $row['RecName'] ?></td>
                            <td><?= $row['RecAdress'] ?></td>
                            <td><?= $row['RecNumber'] ?></td>
                            <td><?= $row['RecPassword'] ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="rec_id" value="<?= $row['RecID'] ?>">
                                    <input type="text" name="rec_name" value="<?= $row['RecName'] ?>" required>
                                    <input type="text" name="rec_address" value="<?= $row['RecAdress'] ?>" required>
                                    <input type="text" name="rec_number" value="<?= $row['RecNumber'] ?>" required>
                                    <input type="password" name="rec_password" value="<?= $row['RecPassword'] ?>" required>
                                    <button type="submit" class="edit-button">Edit</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="rec_id" value="<?= $row['RecID'] ?>">
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
