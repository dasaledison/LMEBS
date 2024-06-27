<?php
// Include TCPDF library
require_once('tcpdf/tcpdf.php');
require_once('includes/load.php'); // Include your database connection file

// Check if report type is provided
if(isset($_GET['report']) && $_GET['report'] === 'all') {
    // Create a new TCPDF instance
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Your Name');
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('All Reservations PDF Report');

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
    $pdf->SetY($pdf->GetY() - 1); // Move the Y position up by the height of the previous cell
    $pdf->Cell(0, 10, 'Date Generated: ' . date('Y-m-d H:i:s'), 0, 1, 'L');
    $pdf->SetY($pdf->GetY() - 1); // Move the Y position up by the height of the previous cell
    $pdf->Cell(0, 10, 'Filter: All Reservations', 0, 1, 'L');

    // Styling for the header "All Reservations"
    $pdf->SetFillColor(50, 66, 145); // Blue background color
    $pdf->SetTextColor(255, 255, 255); // White text color
    $pdf->SetFont('helvetica', 'B', 17);

    // Output header for All Reservations
    $pdf->Cell(0, 10, 'All Reservations', 0, 1, 'C', 1);

    // Output header for the table
    $pdf->SetFillColor(230); // Light grey background color
    $pdf->SetTextColor(0, 0, 0); // Black text color
    $pdf->SetFont('helvetica', '', 8);

    $pdf->Cell(30, 10, 'School ID', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Borrowed From', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Borrowed Till', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Quantity', 1, 0, 'C', 1);
    $pdf->Cell(50, 10, 'Asset Number', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Status', 1, 1, 'C', 1);

    // Fetch all reservations data from the database
    $query = "SELECT borrow_id, school_id, borrowed_from, borrowed_till, quantity, asset_num, status FROM all_borrowers";
    $result = $db->query($query);

    // Output data for All Reservations
    while ($row = $db->fetch_assoc($result)) {
        $pdf->SetFillColor(230); // Light grey background color
        $pdf->SetTextColor(0, 0, 0); // Black text color
        $pdf->SetFont('helvetica', '', 8);

        $pdf->Cell(30, 10, $row['school_id'], 1, 0, 'C');
        $pdf->Cell(30, 10, $row['borrowed_from'], 1, 0, 'C');
        $pdf->Cell(30, 10, $row['borrowed_till'], 1, 0, 'C');
        $pdf->Cell(20, 10, $row['quantity'], 1, 0, 'C');
        $pdf->Cell(50, 10, $row['asset_num'], 1, 0, 'C');
        $pdf->Cell(30, 10, $row['status'], 1, 1, 'C');
    }

    // Output the PDF as an attachment
    ob_end_clean();
    $pdf->Output('all_reservations_report.pdf', 'D');
} else {
    // If report type is not provided or invalid, redirect back to analytics.php or show an error message
    header("Location: analytics.php");
    exit();
}
?>
