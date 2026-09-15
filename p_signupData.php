<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Change as per your database credentials
$password = "";     // Change as per your database credentials
$dbname = "comilla_central_medical"; // Name of your database

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die(json_encode(["error" => "Database connection failed."]));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the request is for validating the contact number
    if (isset($_POST['contact_number']) && !isset($_POST['full_name'])) {
        $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);

        // Query to check if the contact number exists
        $query = "SELECT * FROM patient WHERE ContactNumber = '$contact_number'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            // Contact number already exists in the database
            echo json_encode([
                "exists" => true,
                "message" => "This contact number is already registered."
            ]);
        } else {
            // Contact number is available
            echo json_encode([
                "exists" => false,
                "message" => "This contact number is available."
            ]);
        }
        exit; // Stop further execution for contact number validation
    }

    // If it's a full form submission for registration
    if (isset($_POST['full_name'])) {
        // Sanitize and validate form inputs
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group']);


        // Insert data into the database if contact number is unique
        $sql = "INSERT INTO patient (PatientName, DateOfBirth, Gender, ContactNumber, Email, Password, Address, BloodGroup) 
                VALUES ('$full_name', '$dob', '$gender', '$contact_number', '$email', '$password', '$address', '$blood_group')";

        if (mysqli_query($conn, $sql)) {
            echo json_encode([
                "success" => true,
                "message" => "Signup successful! You can now log in."
                
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Error: " . mysqli_error($conn)
            ]);
        }
    }
}

// Close the connection
mysqli_close($conn);
?>
