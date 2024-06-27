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
    $pdf->SetTitle('Resolved Reports PDF Report');

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

    // Output header for "Confirmed Reports"
    $pdf->SetFillColor(50, 66, 145); // Blue background color
    $pdf->SetTextColor(255, 255, 255); // White text color
    $pdf->SetFont('helvetica', 'B', 17);
    $pdf->Cell(0, 10, 'Resolved Lab Damage Reports', 0, 1, 'C', 1);

    // Output header for the table of confirmed reports
    $pdf->SetFillColor(230); // Light grey background color
    $pdf->SetTextColor(0, 0, 0); // Black text color
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(20, 10, 'Report ID', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Asset Number', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Quantity', 1, 0, 'C', 1);

    $pdf->Cell(20, 10, 'Cause', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Notes', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Resolve Notes', 1, 0, 'C', 1);

    $pdf->Cell(40, 10, 'Report Created', 1, 1, 'C', 1);


    // Fetch confirmed reports from borrower_damage_reports
    $sql_borrower = "SELECT * FROM lab_damage_reports WHERE report_status = 'Resolved'";
    $result_borrower = $db->query($sql_borrower);

    // Output data for borrower damage reports confirmed
    while ($report_borrower = $db->fetch_assoc($result_borrower)) {
        $pdf->SetFillColor(230); // Light grey background color
        $pdf->SetTextColor(0, 0, 0); // Black text color
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(20, 10, $report_borrower['report_id'], 1, 0, 'C'); // Print picture path or name
        $pdf->Cell(30, 10, $report_borrower['asset_num'], 1, 0, 'C'); // Print picture path or name
        $pdf->Cell(20, 10, $report_borrower['quantity'], 1, 0, 'C'); // Print picture path or name

        $pdf->Cell(20, 10, $report_borrower['cause'], 1, 0, 'C');
        $pdf->Cell(30, 10, $report_borrower['notes'], 1, 0, 'C');
        $pdf->Cell(30, 10, $report_borrower['resolve_notes'], 1, 0, 'C');

        $pdf->Cell(40, 10, $report_borrower['report_created'], 1, 0, 'C');

    }

    // Output the PDF as an attachment
    ob_end_clean();
    $pdf->Output('resolved_lab_reports.pdf', 'D');
} else {
    // If report type is not provided or invalid, redirect back to analytics.php or show an error message
    header("Location: analytics.php");
    exit();
}

?>
