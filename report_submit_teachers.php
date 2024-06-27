<?php
// Include necessary files
require_once('includes/config.php');
require_once('includes/load.php');

// Check if the user is logged in
if (!$session->isUserLoggedIn(true)) {
    redirect('index.php', false);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all form fields are set
    if (isset($_POST['reserved_equipment'], $_POST['quantity'], $_POST['date_of_incident'], $_POST['cause'], $_POST['notes'], $_POST['report_status'])) {
        $reserved_equipment_id = $_POST['reserved_equipment'];
        $quantity = $_POST['quantity'];
        $date_of_incident = $_POST['date_of_incident'];
        $cause = $_POST['cause'];
        $notes = $_POST['notes'];
        $report_status = $_POST['report_status']; // Get the selected report status

        // Get the reserved equipment details from the database
        $reserved_equipment = find_by_id('teacher_reserved', $reserved_equipment_id);
        if (!$reserved_equipment || $reserved_equipment['reservation_status'] != 'Completed') {
            $session->msg('d', 'Invalid reservation.');
            redirect('damaged_equipments_teachers.php');
        }

        // Handle file upload using the existing upload_file() function
        $product_img = '';
        if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] === UPLOAD_ERR_OK) {
            $product_img = upload_file('fileToUpload', 'uploads/reports/');
            if (!$product_img) {
                $session->msg('d', 'Failed to upload file.');
                redirect('damaged_equipments_teachers.php');
            }
        }

        // Insert the report into the database
        $report_data = array(
            'equipment' => $reserved_equipment['equipment'],
            'quantity' => $quantity,
            'date_of_incident' => $date_of_incident,
            'cause' => $cause,
            'employee_id' => $reserved_equipment['employee_id'],
            'teacher_name' => $reserved_equipment['teacher_name'],
            'product_id' => $reserved_equipment['product_id'],
            'product_img' => 'uploads/reports/' . $product_img, // Adjust the path here
            'notes' => $notes,
            'report_status' => $report_status // Use the selected report status
        );

        // Update the product quantity if the reservation status is "Confirmed"
        if ($report_status == 'Confirmed') {
            // Get the product details
            $product = find_by_id('products', $reserved_equipment['product_id']);
            if ($product) {
                // Calculate new quantity after confirmation
                $new_quantity = $product['quantity'] - $quantity;
                // Update product quantity
                $update_product_result = update('products', ['quantity' => $new_quantity], ['id' => $reserved_equipment['product_id']]);
                if (!$update_product_result) {
                    $session->msg('d', 'Failed to update product quantity.');
                    redirect('damaged_equipments_teachers.php');
                }
            } else {
                $session->msg('d', 'Product not found.');
                redirect('damaged_equipments_teachers.php');
            }
        }

        // Insert the report into the database
        $insert_result = insert('teachers_damaged_reports', $report_data);
        if ($insert_result) {
            $session->msg('s', 'Report submitted successfully.');
            redirect('damaged_equipments_teachers.php');
        } else {
            $session->msg('d', 'Failed to submit report.');
            redirect('damaged_equipments_teachers.php');
        }
    } else {
        $session->msg('d', 'One or more form fields are missing.');
        redirect('damaged_equipments_teachers.php');
    }
}
?>
