<?php
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

            $sql = "SELECT *FROM patient";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $countPatient = mysqli_num_rows($result);
            
            $sql = "SELECT * FROM `admission` WHERE `AdmissionStatus` LIKE 'Admitted'";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $countAdmittedPatient = mysqli_num_rows($result);
            
            $sql = "SELECT * FROM `doctor`";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $countDoctor = mysqli_num_rows($result);
            
            $sql = "SELECT * FROM `room`";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $countRoom = mysqli_num_rows($result);
            
        ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Dashboard</title>
    <link rel="stylesheet" href="css/r_dashboard.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar Menu -->
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
                <h1>Comilla Central Medical - Dashboard</h1>
                <button class="logout-button">Logout</button>
            </header>
            </form>
        <!-- Overview Section -->
        <section class="overview">
        <div class="card">
                <h4>Total Patients</h4>
                <p><?php echo $countPatient ?></p>
            </div>
            <div class="card">
                <h4>Admitted Patients</h4>
                <p><?php echo $countAdmittedPatient ?></p>
            </div>
            <div class="card">
                <h4>Total Doctors</h4>
                <p><?php echo $countDoctor ?></p>
            </div>
            <div class="card">
                <h4>Available Rooms</h4>
                <p><?php echo $countRoom ?></p>
            </div>

            
            </section>

         <!-- Receptionist -->
                
            <div class="table-container">
            <h2>Receptionists List</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Contact</th>
                </tr>
                <?php while ($row = $receptionists->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['RecID'] ?></td>
                        <td><?= $row['RecName'] ?></td>
                        <td><?= $row['RecAdress'] ?></td>
                        <td><?= $row['RecNumber'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        </div>

       

    


        
    </body>
</html>
