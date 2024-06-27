<?php
// Include necessary files and configurations
require_once('includes/load.php');
require_once('includes/functions.php');

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the barcode from the POST data
    $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';

    // Validate the barcode (you might want to add more validation)
    if (!empty($barcode)) {
        // Search for the product in the database based on the barcode
        $product = find_product_by_equipment_barcode($barcode);

        // Check if the product is found
        if ($product) {
            // Return the product details as JSON response
            header('Content-Type: application/json');
            echo json_encode($product);
        } else {
            // Return an error message if the product is not found
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
        }
    } else {
        // Return an error message if the barcode is empty
        http_response_code(400);
        echo json_encode(['error' => 'Invalid barcode']);
    }
} else {
    // Return an error message for invalid requests
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
}
?>
