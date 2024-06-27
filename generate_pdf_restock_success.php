<?php
// Include TCPDF library
require_once('tcpdf/tcpdf.php');
require_once('includes/load.php'); // Include your database connection file

// Check if report type is provided
if (isset($_GET['report'])) {
    // Get the current user's ID
    $user_id = $_SESSION['user_id']; // Assuming the session variable is set with the user's ID

    // Fetch the current user's name from the database
    $sql_user = "SELECT name FROM users WHERE id = '{$user_id}'";
    $result_user = $db->query($sql_user);
    $user = $db->fetch_assoc($result_user);
    $admin_name = isset($user['name']) ? $user['name'] : 'Unknown'; // Default to 'Unknown' if the user is not found

    // Create a new TCPDF instance
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('Your Name');
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Product Report');

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

    // Add Admin Name, Date Generated, and Filter information for Products
    $pdf->Cell(0, 10, 'Admin Name: ' . $admin_name, 0, 1, 'L');
    $pdf->SetY($pdf->GetY() - 1); // Move the Y position up by the height of the previous cell
    $pdf->Cell(0, 10, 'Date Generated: ' . date('Y-m-d H:i:s'), 0, 1, 'L');
    $pdf->SetY($pdf->GetY() - 1); // Move the Y position up by the height of the previous cell
    $pdf->Cell(0, 10, 'Filter: All Products', 0, 1, 'L');

    // Styling for the header "All Products"
    $pdf->SetFillColor(50, 66, 145); // Blue background color
    $pdf->SetTextColor(255, 255, 255); // White text color
    $pdf->SetFont('helvetica', 'B', 17);

    // Output header for All Products
    $pdf->Cell(0, 10, 'All Products Restock Success', 0, 1, 'C', 1);

    // Output header for the table
    $pdf->SetFillColor(230); // Light grey background color
    $pdf->SetTextColor(0, 0, 0); // Black text color
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(20, 10, 'Product ID', 1, 0, 'C', 1);
    $pdf->Cell(50, 10, 'Product Name', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Quantity', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Expected Quantity', 1, 0, 'C', 1);
    $pdf->Cell(60, 10, 'Restock Percentage', 1, 1, 'C', 1);

    // Fetch product data from the database
    $query = "SELECT product_id, name, quantity, expected_quantity FROM products";
    $result = $db->query($query);

    // Output data for Products
    while ($row = $db->fetch_assoc($result)) {
        $pdf->SetFillColor(230); // Light grey background color
        $pdf->SetTextColor(0, 0, 0); // Black text color
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(20, 10, $row['product_id'], 1, 0, 'C');
        $pdf->Cell(50, 10, $row['name'], 1, 0, 'C');
        $pdf->Cell(30, 10, $row['quantity'], 1, 0, 'C');
        $pdf->Cell(30, 10, $row['expected_quantity'], 1, 0, 'C');

        // Calculate the restock success percentage
        $restockPercentage = min(($row['quantity'] / $row['expected_quantity']) * 100, 100);

        $pdf->Cell(60, 10, number_format($restockPercentage, 2) . '%', 1, 1, 'C');
    }

    // Output the PDF as an attachment
    ob_end_clean();
    $pdf->Output('product_report.pdf', 'D');
} else {
    // If report type is not provided or invalid, redirect back to analytics.php or show an error message
    header("Location: analytics.php");
    exit();
}
?>
