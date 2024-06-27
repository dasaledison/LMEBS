<?php
require_once('includes/load.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productId = $_POST['productId'];
    $barcode = $_POST['barcode'];

    // Update equipment_barcode in the database
    $sql = "UPDATE products SET equipment_barcode = '$barcode' WHERE id = $productId";
    if ($db->query($sql)) {
        echo "Barcode updated successfully";
    } else {
        echo "Error updating barcode: " . $db->error;
    }
}
?>
