```php
<?php

session_start();

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get patient information from form
    $patientID = $_POST['patient-id'] ?? null;
    $patientName = $_POST['fullname'] ?? '';
    $contactNumber = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $doctorID = $_POST['doctor'] ?? '';
    $appointmentDate = $_POST['date'] ?? '';

    // Basic validation
    if (
        empty($patientName) ||
        empty($contactNumber) ||
        empty($email) ||
        empty($gender) ||
        empty($dob) ||
        empty($doctorID) ||
        empty($appointmentDate)
    ) {
        die("Please fill in all required fields.");
    }

    try {

        /*
         * Check whether patient already exists
         */
        if (!empty($patientID)) {

            // Existing patient
            $sql = "UPDATE Patient
                    SET PatientName = ?,
                        ContactNumber = ?,
                        Email = ?,
                        Gender = ?,
                        DateOfBirth = ?
                    WHERE PatientID = ?";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param(
                "sssssi",
                $patientName,
                $contactNumber,
                $email,
                $gender,
                $dob,
                $patientID
            );

            $stmt->execute();

        } else {

            /*
             * New patient
             */

            $password = "12345678";

            $sql = "INSERT INTO Patient
                    (
                        PatientName,
                        ContactNumber,
                        Email,
                        Gender,
                        DateOfBirth,
                        Address,
                        BloodGroup,
                        Password
                    )
                    VALUES (?, ?, ?, ?, ?, NULL, NULL, ?)";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param(
                "ssssss",
                $patientName,
                $contactNumber,
                $email,
                $gender,
                $dob,
                $password
            );

            $stmt->execute();

            // Get newly created PatientID
            $patientID = $stmt->insert_id;
        }

        $stmt->close();


        /*
         * Insert appointment
         */
        $appointmentSQL = "INSERT INTO Appointment
                           (
                               PatientID,
                               DoctorID,
                               AppointmentDate,
                               Status
                           )
                           VALUES (?, ?, ?, 'Pending')";

        $appointmentStmt = $conn->prepare($appointmentSQL);

        if (!$appointmentStmt) {
            die("Appointment prepare failed: " . $conn->error);
        }

        $appointmentStmt->bind_param(
            "iis",
            $patientID,
            $doctorID,
            $appointmentDate
        );

        $appointmentStmt->execute();

        $appointmentStmt->close();


        /*
         * Appointment successfully created
         */
        echo "<script>
                alert('Patient appointment made successfully!');
                window.location.href='appointment.php';
              </script>";

    } catch (Exception $e) {

        echo "Error: " . $e->getMessage();
    }
}

?>
```
