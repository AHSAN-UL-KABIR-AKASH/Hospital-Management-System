<?php
session_start();
include 'db.php';

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $entity = $_POST['entity'] ?? '';
    $action = $_POST['action'] ?? '';

    if ($entity === 'test') {
        if ($action === 'create') {
            $testName = trim($_POST['test_name']);
            $price = floatval($_POST['price']);

            if (!empty($testName) && $price > 0) {
                $stmt = $conn->prepare("INSERT INTO test (TestName, TestPrice) VALUES (?, ?)");
                if ($stmt) {
                    $stmt->bind_param("sd", $testName, $price);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    error_log("Database error: " . $conn->error);
                }
            }
        } elseif ($action === 'delete') {
            $testId = intval($_POST['test_id']);
            if ($testId > 0) {
                $stmt = $conn->prepare("DELETE FROM test WHERE TestID = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $testId);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    error_log("Database error: " . $conn->error);
                }
            }
        } elseif ($action === 'edit') {
            $testId = intval($_POST['test_id']);
            $testName = trim($_POST['test_name']);
            $price = floatval($_POST['price']);

            if ($testId > 0 && !empty($testName) && $price > 0) {
                $stmt = $conn->prepare("UPDATE test SET TestName = ?, TestPrice = ? WHERE TestID = ?");
                if ($stmt) {
                    $stmt->bind_param("sdi", $testName, $price, $testId);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    error_log("Database error: " . $conn->error);
                }
            }
        }
    }

    // Redirect to prevent form resubmission
    header('Location: manageTests.php');
    exit;
}

// Fetch data for display
$tests = $conn->query("SELECT * FROM Test");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tests</title>
    <link rel="stylesheet" href="css/manageTests.css">
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
        <!-- <form action="logout.php"> -->
            <header>
                <h1>Comilla Central Medical</h1>
                <button class="logout-button">Logout</button>
            </header>
            <!-- </form> -->

            <div class="content">
                <h2>Register New Test</h2>
                <div class="input-container">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="entity" value="test">
                        <input type="hidden" name="action" value="create">
                        <input type="text" name="test_name" placeholder="Test Name" required>
                        <input type="number" name="price" placeholder="Price" step="0.01" required>
                        <button type="submit" class="add-test-button">Add Test</button>
                    </form>
                </div>

                <table>
                    <tr>
                        <th>Test ID</th>
                        <th>Test Name</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($row = $tests->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['TestID']) ?></td>
                            <td><?= htmlspecialchars($row['TestName']) ?></td>
                            <td><?= htmlspecialchars($row['TestPrice']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="entity" value="test">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="test_id" value="<?= htmlspecialchars($row['TestID']) ?>">
                                    <button type="submit" class="delete-button">Delete</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="entity" value="test">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="test_id" value="<?= htmlspecialchars($row['TestID']) ?>">
                                    <input type="text" name="test_name" value="<?= htmlspecialchars($row['TestName']) ?>" required>
                                    <input type="number" name="price" value="<?= htmlspecialchars($row['TestPrice']) ?>" step="0.01" required>
                                    <button type="submit" class="edit-button">Edit</button>
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
