<?php
// Include the FPDF library
require('fpdf/fpdf.php');

// Include the database connection (make sure db_connection.php is correctly included)
include 'p_profileValid.php';

// Start session
session_start();

// Get the patient ID and patient name from the session
$patient_id = $_SESSION['user_id'];
$patient_name = $_SESSION['user_name']; // Assuming the patient name is stored in the session

// Fetch prescriptions for the patient
$sql = "SELECT p.Date, p.PrescriptionDescription, d.DoctorName 
        FROM Prescription p
        JOIN Doctor d ON p.DoctorID = d.DoctorID
        WHERE p.PatientID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

// Create a new PDF instance
$pdf = new FPDF();
$pdf->AddPage();

// Set background color
$pdf->SetFillColor(240, 240, 240);  // Light gray background color
$pdf->Rect(0, 0, 210, 297, 'F');  // Set the background for the whole page

// Set title font for the header
$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(0, 51, 102);  // Set title text color (dark blue)
$pdf->Cell(0, 10, 'Comilla Central Medical', 0, 1, 'C'); // Centered title
$pdf->Ln(5); // Line break

// Set the subtitle (Medical Prescription)
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Medical Prescription', 0, 1, 'C');
$pdf->Ln(10); // Line break

// Add Patient Information Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Patient Name: ' . htmlspecialchars($patient_name), 0, 1); // Patient Name
$pdf->Ln(5); // Line break

// Set font for the content
$pdf->SetFont('Arial', '', 12);

// Check if there are any prescriptions
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Add Prescription Details Section
        $pdf->SetFont('Arial', 'B', 12);  // Bold for Doctor's Name
        $pdf->Cell(0, 10, 'Prescription Date: ' . (new DateTime($row['Date']))->format('F j, Y'), 0, 1);
        $pdf->SetFont('Arial', 'B', 12);  // Bold for Doctor's Name
        $pdf->Cell(0, 10, 'Prescribed by: ' . htmlspecialchars($row['DoctorName']), 0, 1); // Doctor's name in bold
        $pdf->SetFont('Arial', '', 12);  // Set back to regular font
        $pdf->Ln(10); // Line break
        
        // Add Prescription Details with proper formatting
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Prescription Details:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, htmlspecialchars($row['PrescriptionDescription']));
        $pdf->Ln(5); // Small line break
        $pdf->Cell(0, 10, '--------------------------------------------------------', 0, 1); // Divider line
        $pdf->Ln(5); // Line break between prescriptions
    }
} else {
    // If no prescriptions are found, display a message
    $pdf->Cell(0, 10, 'No prescriptions found for this patient.', 0, 1);
}

// Output the PDF (force download)
$pdf->Output('D', 'Prescription.pdf');

// Close the database connection
$stmt->close();
$conn->close();
?>
