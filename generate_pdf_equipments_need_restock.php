<?php
// Include TCPDF library
require_once('tcpdf/tcpdf.php');
require_once('includes/load.php'); // Include your database connection file

// Check if report type is provided
if(isset($_GET['report'])) {
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
    $pdf->SetTitle('Products PDF Report');

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

    // Output header for "Products Need Restocking"
    $pdf->SetFillColor(50, 66, 145); // Blue background color
    $pdf->SetTextColor(255, 255, 255); // White text color
    $pdf->SetFont('helvetica', 'B', 17);
    $pdf->Cell(0, 10, 'Products Need Restocking', 0, 1, 'C', 1);

    // Output header for the table of products needing restocking
    $pdf->SetFillColor(230); // Light grey background color
    $pdf->SetTextColor(0, 0, 0); // Black text color
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(15, 10, 'No', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Name', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Qty', 1, 0, 'C', 1);
    $pdf->Cell(55, 10, 'Asset Num', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Expected Qty', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Percentage', 1, 1, 'C', 1);

    // Fetch products needing restocking from the database
    $sql = "SELECT p.*, c.name AS category_name 
            FROM products p
            LEFT JOIN categories c ON p.categorie_id = c.id
            WHERE p.expected_quantity > p.quantity";
    $result = $db->query($sql);

    // Output data for products needing restocking
    $index = 0;
    while ($product = $db->fetch_assoc($result)) {
        $index++;
        $pdf->SetFillColor(230); // Light grey background color
        $pdf->SetTextColor(0, 0, 0); // Black text color
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(15, 10, $index, 1, 0, 'C');
        $pdf->Cell(40, 10, $product['name'], 1, 0, 'C');
        $pdf->Cell(20, 10, $product['quantity'], 1, 0, 'C');
        $pdf->Cell(55, 10, $product['asset_num'], 1, 0, 'C');
        $pdf->Cell(20, 10, $product['expected_quantity'], 1, 0, 'C');
        // Calculate percentage
      // Calculate percentage
$percentage = ($product['expected_quantity'] - $product['quantity']) / $product['expected_quantity'] * 100;
$pdf->Cell(40, 10, round($percentage, 2) . '%', 1, 1, 'C');

    }
// Add a break space between the tables
$pdf->Ln(10); // Add a line break with 10 units of height

    // Output header for "Other Equipments"
    $pdf->SetFillColor(82, 82, 82); // #525252 background color
    $pdf->SetTextColor(255, 255, 255); // White text color
    $pdf->SetFont('helvetica', 'B', 12); // Bold font
    $pdf->Cell(0, 10, 'Other Equipments', 0, 1, 'C', 1); // Cell with border and background color
    // Output header for the table of other equipments
    $pdf->SetFillColor(230); // Light grey background color
    $pdf->SetTextColor(0, 0, 0); // Black text color
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(15, 10, 'No', 1, 0, 'C', 1);
    $pdf->Cell(50, 10, 'Name', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Qty', 1, 0, 'C', 1);
    $pdf->Cell(65, 10, 'Asset Num', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Expected Qty', 1, 1, 'C', 1);

    // Fetch products not needing restocking from the database
    $sql = "SELECT p.*, c.name AS category_name 
            FROM products p
            LEFT JOIN categories c ON p.categorie_id = c.id
            WHERE p.expected_quantity <= p.quantity
            ORDER BY p.expected_quantity DESC"; // Order by expected_quantity
    $result = $db->query($sql);

    // Output data for other equipments
    $index = 0;
    while ($product = $db->fetch_assoc($result)) {
        $index++;
        $pdf->SetFillColor(230); // Light grey background color
        $pdf->SetTextColor('#525252'); // Black text color
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(15, 10, $index, 1, 0, 'C');
        $pdf->Cell(50, 10, $product['name'], 1, 0, 'C');
        $pdf->Cell(30, 10, $product['quantity'], 1, 0, 'C');
        $pdf->Cell(65, 10, $product['asset_num'], 1, 0, 'C');
        $pdf->Cell(30, 10, $product['expected_quantity'], 1, 1, 'C');
    }

    // Output the PDF as an attachment
    ob_end_clean();
    $pdf->Output('products_report.pdf', 'D');
} else {
    // If report type is not provided or invalid, redirect back to analytics.php or show an error message
    header("Location: analytics.php");
    exit();
}
?>
