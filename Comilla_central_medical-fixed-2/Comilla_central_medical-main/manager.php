<?php
session_start();
// Database configuration
$host = 'localhost'; 
$username = 'root'; 
$password = ''; 
$dbname = 'comilla_central_medical'; 

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entity = $_POST['entity'] ?? '';
    $action = $_POST['action'] ?? '';

    if ($entity === 'room' && $action === 'create') {
        $roomNumber = $_POST['room_number'];
        $roomType = $_POST['room_type'];
        $pricePerDay = $_POST['price_per_day'];

        if (!empty($roomNumber) && !empty($roomType) && !empty($pricePerDay)) {
            $stmt = $conn->prepare("INSERT INTO room (RoomNumber, RoomType, RoomPrice) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $roomNumber, $roomType, $pricePerDay);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($entity === 'room' && $action === 'delete') {
        $roomId = $_POST['room_id'];
        if (!empty($roomId)) {
            $stmt = $conn->prepare("DELETE FROM room WHERE RoomID = ?");
            $stmt->bind_param("i", $roomId);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Fetch data for display
$rooms = $conn->query("SELECT * FROM room");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms</title>
    <link rel="stylesheet" href="sempleMenuBar/sempleMenuBar.css">
    <link rel="stylesheet" href="css/manager.css">
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
            <form action="logout.php" method="POST">
                <header>
                    <h1>Comilla Central Medical</h1>
                    <button class="logout-button">Logout</button>
                </header>
            </form>

            <!-- Modify Rooms Section -->
            <div class="content">
                <h2 style="text-align: center;">Add Room</h2>
                <div class="input-container">
                    <form method="POST">
                        <input type="hidden" name="entity" value="room">
                        <input type="hidden" name="action" value="create">
                        <input type="text" name="room_number" placeholder="Room Number" required><br>
                        <select id="room_type" name="room_type" required>
                            <option value="Single">Single</option>
                            <option value="Double">Double</option>
                            <option value="Suit">Suit</option>
                        </select>
                        <input type="text" name="price_per_day" placeholder="Price Per Day" required><br>
                        <button type="submit" class="add-receptionist-button">Add Room</button>
                    </form>
                </div>

                <!-- Display Rooms -->
                <div class="table-container">
                    <h2>Rooms List</h2>
                    <table>
                        <tr>
                            <th>Room ID</th>
                            <th>Room Number</th>
                            <th>Type</th>
                            <th>Price Per Day</th>
                            <th>Actions</th>
                        </tr>
                        <?php while ($row = $rooms->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['RoomID'] ?></td>
                                <td><?= $row['RoomNumber'] ?></td>
                                <td><?= $row['RoomType'] ?></td>
                                <td><?= $row['RoomPrice'] ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="entity" value="room">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="room_id" value="<?= $row['RoomID'] ?>">
                                        <button type="submit" class="delete-button">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
