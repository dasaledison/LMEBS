<?php
// Include necessary files and functions
require_once('includes/load.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve student ID and barcode from the POST request
    $studentId = $_POST['studentId'];
    $barcode = $_POST['barcode'];

    // Update student_barcode in the database
    $sql = "UPDATE students SET student_barcode = ? WHERE student_id = ?";
    
    // Prepare and execute the SQL statement with parameterized queries
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $barcode, $studentId);
    if ($stmt->execute()) {
        echo "Barcode updated successfully";
    } else {
        echo "Error updating barcode: " . $stmt->error;
    }
    $stmt->close();
} else {
    // Handle invalid request method
    echo "Invalid request method";
}
?>
