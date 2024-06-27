<?php
// Include TCPDF library
require_once('tcpdf/tcpdf.php');
require_once('includes/load.php'); // Include your database connection file

// Check if report type is provided
if (isset($_GET['report'])) {
    // Create a new TCPDF instance
    $user_id = $_SESSION['user_id']; // Assuming the session variable is set with the user's ID

    // Fetch the current user's name from the database
    $sql_user = "SELECT name FROM users WHERE id = '{$user_id}'";
    $result_user = $db->query($sql_user);
    $user = $db->fetch_assoc($result_user);
    $admin_name = isset($user['name']) ? $user['name'] : 'Unknown'; // Default to 'Unknown' if user not found

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

    // Add converted PNG logo to the PDF
    $pdf->Image($logoPath, $pdf->GetPageWidth() / 2 - 30, 11, 60);

    // Set the cursor below the logo
    $pdf->SetY(40);

    // Add Admin Name, Date Generated, and Filter information
    $pdf->Cell(0, 10, 'Admin Name: ' . $admin_name, 0, 1, 'L');
    $pdf->SetY($pdf->GetY() - 1); // Move the Y position up by the height of the previous cell
    $pdf->Cell(0, 10, 'Date Generated: ' . date('Y-m-d H:i:s'), 0, 1, 'L');

    // Styling for the header "Confirmed Reports"
    $pdf->SetFillColor(50, 66, 145); // Blue background color
    $pdf->SetTextColor(255, 255, 255); // White text color
    $pdf->SetFont('helvetica', 'B', 17);

    // Output header for Confirmed Reports
    $pdf->Cell(0, 10, 'Confirmed Reports', 0, 1, 'C', 1);

    // Output header for the table
    $pdf->SetFillColor(230); // Light grey background color
    $pdf->SetTextColor(0, 0, 0); // Black text color
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(20, 10, 'Equipment', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Quantity', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Date of Incident', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Cause', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'ID', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Student/Teacher Name', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Notes', 1, 1, 'C', 1);

    // Fetch confirmed reports data from the database using appropriate functions
    $confirmed_student_reports = find_all_student_damaged_reports(); // Assuming you have this function
    $confirmed_teacher_reports = find_all_teachers_damaged_reports(); // Assuming you have this function

    // Output data for confirmed student reports
    foreach ($confirmed_student_reports as $index => $report) {
        outputReportRow($pdf, $index, $report);
    }

    // Output data for confirmed teacher reports
    foreach ($confirmed_teacher_reports as $index => $report) {
        outputReportRow($pdf, $index, $report);
    }

    // Output the PDF as an attachment
    ob_end_clean();
    $pdf->Output('confirmed_reports.pdf', 'D');
} else {
    // If report type is not provided or invalid, redirect back to analytics.php or show an error message
    header("Location: analytics.php");
    exit();
}

// Function to output a row in the PDF
function outputReportRow($pdf, $index, $report)
{
    $pdf->SetFillColor(230); // Light grey background color
    $pdf->SetTextColor(0, 0, 0); // Black text color
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(20, 10, $report['equipment'], 1, 0, 'C');
    $pdf->Cell(20, 10, $report['quantity'], 1, 0, 'C');
    $pdf->Cell(30, 10, $report['date_of_incident'], 1, 0, 'C');
    $pdf->Cell(30, 10, $report['cause'], 1, 0, 'C');
    $pdf->Cell(20, 10, $report['student_id'], 1, 0, 'C');
    $pdf->Cell(40, 10, $report['student_name'], 1, 0, 'C');
    $pdf->Cell(30, 10, $report['notes'], 1, 1, 'C');
}
?>
