<?php 
     include('p_changepassValid.php'); // Include the PHP logic
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Comilla Central Medical</title>
    <link rel="stylesheet" href="css/p_change_pass.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
        <h2>Menu</h2>
            <ul>
                <li><a href="p_dashboard.php">Dashboard</a></li>
                <li><a href="p_profile.php">Profile</a></li>
                <li><a href="appointment.php">Make Appointment</a></li>
                <li><a href="p_prescription.php">Prescription</a></li>
                <li><a href="p_view_appointment.php">View Appointment</a></li>
                <li><div class="settings-menu">
                    <a href="#settings" id="settings-icon">Settings</a>
                    <div class="dropdown" id="settings-dropdown" style="display: none;">
                        <a href="p_change_pass.php">Change Password</a>
                    </div>
                </div></li>
                <li><a href="#help">Help</a></li>
            </ul>
        </nav>
        </nav>

        <div class="main-content">
            <header>
                <h1>Comilla Central Medical</h1>
                <form action="logout.php" method="POST">
                    <button type="submit" class="logout-button">Logout</button>
                </form>
            </header> 

            <!-- Success/Error messages -->
            <?php if (!empty($success_message)) : ?>
                <p class="successs"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (!empty($error_message)) : ?>
                <p class="errorr"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <!-- Change Password Form -->
            <form class="appointment-form" action="p_change_pass.php" method="POST">
                <h1>Change Password</h1>
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" id="current_password" required>

                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required>
                <p id="password_error"></p>

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>

                <button type="submit" class="btn-group">Change Password</button>
            </form>
        </div>
    </div>
    <script src="p_changepass.js"></script>
</body>
</html>
