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
    $pdf->SetTitle('In Use Equipments PDF Report');

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


    // Styling for the header "In Use Equipments"
    $pdf->SetFillColor(50, 66, 145); // Blue background color
    $pdf->SetTextColor(255, 255, 255); // White text color
    $pdf->SetFont('helvetica', 'B', 17);

    // Output header for In Use Equipments
    $pdf->Cell(0, 10, 'In Use Equipments', 0, 1, 'C', 1);

    // Output header for the table
    $pdf->SetFillColor(230); // Light grey background color
    $pdf->SetTextColor(0, 0, 0); // Black text color
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(10, 10, 'No', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Name', 1, 0, 'C', 1);
    $pdf->Cell(10, 10, 'Qty', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Item No.', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Asset No.', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Brand', 1, 0, 'C', 1);
    $pdf->Cell(10, 10, 'Room', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Description', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Equipment Barcode', 1, 1, 'C', 1);

    // Fetch product data with category from the database using the function
    $products = find_in_use_products();

    // Output data for products
    foreach ($products as $index => $product) {
        $pdf->SetFillColor(230); // Light grey background color
        $pdf->SetTextColor(0, 0, 0); // Black text color
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Cell(10, 10, $index + 1, 1, 0, 'C');
        $pdf->Cell(20, 10, $product['Name'], 1, 0, 'C');
        $pdf->Cell(10, 10, $product['Qty'], 1, 0, 'C');
        $pdf->Cell(20, 10, $product['Item No.'], 1, 0, 'C');
        $pdf->Cell(30, 10, $product['Asset No.'], 1, 0, 'C');
        $pdf->Cell(20, 10, $product['Brand'], 1, 0, 'C');
        $pdf->Cell(10, 10, $product['Room'], 1, 0, 'C');
        $pdf->Cell(20, 10, $product['Description'], 1, 0, 'C');
        $pdf->Cell(40, 10, $product['Equipment Barcode'], 1, 1, 'C');
    }

    // Output the PDF as an attachment
    ob_end_clean();
    $pdf->Output('in_use_products_report.pdf', 'D');
} else {
    // If report type is not provided or invalid, redirect back to analytics.php or show an error message
    header("Location: analytics.php");
    exit();
}

// Function to fetch products with status "In Use"
function find_in_use_products() {
    global $db;
    $sql = "SELECT borrow_id, school_id, borrowed_from, borrowed_till, subject_id, quantity AS Qty, asset_num AS `Asset No.`, status, 
                    name AS Name, item_no AS `Item No.`, brand AS Brand, room AS Room, description AS Description, equipment_barcode AS `Equipment Barcode`
            FROM all_borrowers 
            WHERE status = 'In Use'";
    $result = $db->query($sql);
    $products = [];
    while ($row = $db->fetch_assoc($result)) {
        $products[] = $row;
    }
    return $products;
}

?>
