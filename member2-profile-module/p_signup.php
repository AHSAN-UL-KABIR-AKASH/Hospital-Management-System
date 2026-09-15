<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Signup</title>
    <link rel="stylesheet" href="css/p_signup.css">

</head>
<body>
    
        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>Comilla Central Medical</h1>
                <p>You have Olready Account <a href="loginPage.php">Please Login</a></p>
            </header>

            <!-- Signup Form -->
            <form class="appointment-form" action="p_signupData.php" method="post" onsubmit="return validateForm()">
                <h1>Patient Signup</h1>
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required>
                    <small class="error" id="name_error"></small>
 
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" required>
                    <small class="error" id="contact_error"></small>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                    <small class="error" id="email_error"></small>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <small class="error" id="password_error"></small>
                    
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" required>
                </div>
                <div class="form-group">
                    <label for="blood_group">Blood Group</label>
                    <select id="blood_group" name="blood_group">
                        <option value="">Select Blood Group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                    </select>
                </div>
                <div class="btn-group">
                    <button type="submit">Signup</button>
                </div>
            </form>
        </div>
    <script src="p_signup.js"></script>
</body>
</html>
