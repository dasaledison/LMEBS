<?php
// Include necessary files and functions
require_once('includes/load.php');
require_once('includes/functions.php');

// Check the user's permission level
page_require_level(2);

// Fetch enrolled students for the subject
$enrolled_students = find_students_by_subject_id($subject_id);

// Use a PDF generation library (e.g., TCPDF, FPDF) to create a PDF
// Here, I'll use TCPDF as an example
require_once('includes/tcpdf/tcpdf.php');

// Create a new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Your Name');
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Confirmed Reports PDF Report');

// Add a page
$pdf->AddPage();

// Set font size to 10px
$pdf->SetFont('helvetica', '', 10);

// Set cell padding to 1
$pdf->setCellPadding(0);

// Add logo to the top middle
$logoPath = 'libs/images/LOGO.png';

// Add header row to the PDF
$pdf->Cell(30, 10, 'Barcode', 1);
$pdf->Cell(50, 10, 'Student Name', 1);
$pdf->Cell(50, 10, 'Student ID', 1);
$pdf->Ln(); // Move to the next line

// Add data rows to the PDF
foreach ($enrolled_students as $student) {
    // Generate barcode for each student (you may adjust the code based on your barcode generation logic)
    $barcode = generate_barcode($student['student_id']);

    // Add a row to the PDF
    $pdf->Cell(30, 10, $barcode, 1);
    $pdf->Cell(50, 10, $student['name'], 1);
    $pdf->Cell(50, 10, $student['student_id'], 1);
    $pdf->Ln(); // Move to the next line
}

// Output the PDF to the browser
$pdf->Output('enrolled_students_barcodes.pdf', 'I');
?>
