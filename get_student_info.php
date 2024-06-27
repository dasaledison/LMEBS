<?php
// Include necessary files and initialize session if needed
require_once('includes/functions.php');
require_once('includes/load.php');
// page_require_level(1); // Adjust the required user level as needed

// Check if the borrow ID is provided in the GET request
if (isset($_GET['borrow_id'])) {
    $borrow_id = (int)$_GET['borrow_id']; // Sanitize input as integer

    // Fetch student information based on the provided borrow ID
    $student_info = find_by_sql("SELECT student_name, student_id, asset_num, product_name FROM your_student_table WHERE borrow_id = {$borrow_id}");

    if ($student_info) {
        // If student information is found, return it as JSON response
        $response = array(
            'student_name' => $student_info['student_name'],
            'student_id' => $student_info['student_id'],
            'asset_num' => $student_info['asset_num'],
            'product_name' => $student_info['product_name']
        );
        echo json_encode($response);
    } else {
        // If no student information is found, return an empty response
        echo json_encode(array());
    }
} else {
    // If no borrow ID is provided, return an error response
    echo json_encode(array('error' => 'No borrow ID provided'));
}
?>
