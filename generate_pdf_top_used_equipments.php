<?php
// Include TCPDF library
require_once('tcpdf/tcpdf.php');
require_once('includes/load.php'); // Include your database connection file

// Check if report type is provided
if(isset($_GET['report'])) {
    // Get the current user's ID
    $user_id = $_SESSION['user_id']; // Assuming the session variable is set with the user's ID

    // Fetch the current user's name from the database
    $sql_user = "SELECT name FROM users WHERE id = '{$user_id}'";
    $result_user = $db->query($sql_user);
    $user = $db->fetch_assoc($result_user);
    $admin_name = isset($user['name']) ? $user['name'] : 'Unknown'; // Default to 'Unknown' if user not found

    // Create a new TCPDF instance
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Your Name');
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Generated PDF Report');

    // Add a page
    $pdf->AddPage();

    // Set font size to 10px
    $pdf->SetFont('helvetica', '', 10);

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
    $pdf->SetY($pdf->GetY() - 1); // Move the Y position up by the height of the previous cell
    $pdf->Cell(0, 10, 'Filter: All time', 0, 1, 'L');
    
    // Generate content based on the report type
    switch($_GET['report']) {
        case 'top_used_equipments':
            // Fetch top used equipments data from the database
            $query = "SELECT p.name AS equipment, 
                             IFNULL(SUM(ab.quantity), 0) AS total_usage 
                      FROM products p
                      LEFT JOIN all_borrowers ab ON p.asset_num = ab.asset_num
                      GROUP BY p.asset_num 
                      ORDER BY total_usage DESC";
            $result = $db->query($query);

            // Styling for the header "Top Used Equipments"
            $pdf->SetFillColor(50, 66, 145); // Blue background color
            $pdf->SetTextColor(255, 255, 255); // White text color
            $pdf->SetFont('helvetica', 'B', 17);

            // Output header for Top Used Equipments
            $pdf->Cell(0, 10, 'Top Used Equipments', 0, 1, 'C', 1);

            // Initialize total usage for percentage calculation
            $total_usage_all = 0;
            while ($row = $result->fetch_assoc()) {
                $total_usage_all += $row['total_usage'];
            }

            // Reset the result set to display data
            $result->data_seek(0);

            // Styling for the content
            $pdf->SetFillColor(230); // Light grey background color
            $pdf->SetTextColor(0, 0, 0); // Black text color
            $pdf->SetFont('helvetica', '', 12);

            // Output header for "Equipment Total Usage Percentage"
            $pdf->Cell(80, 10, 'Equipment', 1, 0, 'C', 1);
            $pdf->Cell(40, 10, 'Total Usage', 1, 0, 'C', 1);
            $pdf->Cell(70, 10, 'Percentage', 1, 1, 'C', 1);

            // Display equipment, total usage, and percentage
            if ($total_usage_all > 0) { // Check if total_usage_all is not zero
                while ($row = $result->fetch_assoc()) {
                    $equipment = $row['equipment'];
                    $total_usage = $row['total_usage'];
                    $percentage = number_format(($total_usage / $total_usage_all) * 100, 2) . '%';

                    $pdf->SetFillColor(255); // White background color
                    $pdf->Cell(80, 10, $equipment, 1, 0, 'C', 0);
                    $pdf->Cell(40, 10, $total_usage, 1, 0, 'C', 0);
                    $pdf->Cell(70, 10, $percentage, 1, 1, 'C', 0);
                }
            } else {
                $pdf->Cell(0, 10, 'No data available', 0, 1, 'C'); // Display a message if there is no data
            }

            // Add total reserved all time below the table
            $pdf->Cell(0, 10, 'Total Borrowed All Time: ' . $total_usage_all, 0, 1, 'C');

            break;

        // Add cases for other reports here

        default:
            $pdf->Cell(0, 10, 'Invalid Report Type', 0, 1, 'C');
    }

    // Output the PDF as an attachment
    ob_end_clean();
    $pdf->Output('report.pdf', 'D');
    exit(); // Make sure to exit after generating and outputting the PDF
} else {
    // If report type is not provided, redirect back to analytics.php or show an error message
    header("Location: analytics.php");
    exit();
}
?>
