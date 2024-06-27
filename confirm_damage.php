<?php
require_once('includes/load.php');

// Check if an ID is provided in the URL
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $damaged_equipment = find_by_id('student_damaged_reports', $id);

    // Check if damaged equipment record is found
    if (!$damaged_equipment) {
        exit("Damaged equipment report not found for ID: $id");
    }

    // Check if the report status is eligible for confirmation
    if (!in_array($damaged_equipment['report_status'], ['Pending', 'Under Review', 'Cancelled'])) {
        exit("Invalid status for confirmation: {$damaged_equipment['report_status']}");
    }

    // Get the product ID and quantity from the damaged equipment report
    $product_id = $damaged_equipment['product_id'];
    $quantity = $damaged_equipment['quantity'];

    // Get the product information
    $product = find_by_id('products', $product_id);

    // Check if product exists
    if (!$product) {
        exit("Product not found for ID: $product_id");
    }

    // Debugging: Output product quantity and requested quantity
    echo "Product Quantity: {$product['quantity']}<br>";
    echo "Requested Quantity: $quantity<br>";

    // Check if there's sufficient quantity in stock
    if ($product['quantity'] >= $quantity) {
        // Calculate new quantity after damage confirmation
        $new_quantity = $product['quantity'] - $quantity;

        // Update the products table with new quantity
        $update_product_result = update('products', ['quantity' => $new_quantity], ['id' => $product_id]);

        // Check if product quantity update was successful
        if (!$update_product_result) {
            exit("Failed to update product quantity for ID: $product_id");
        }
    } else {
        exit("Insufficient quantity in stock for product ID: $product_id");
    }

    // Update the report status to 'Confirmed'
    $update_report_result = update('student_damaged_reports', ['report_status' => 'Confirmed'], ['id' => $id]);

    // Check if report status update was successful
    if (!$update_report_result) {
        exit("Failed to update report status for ID: $id");
    }

    // Redirect back to the damaged_equipments page
    redirect('damaged_equipments.php');
} else {
    // No ID provided in the URL
    exit("ID not provided.");
}
?>
