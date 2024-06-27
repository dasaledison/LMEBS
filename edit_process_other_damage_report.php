<?php
require_once('includes/functions.php');
require_once('includes/load.php');
page_require_level(1); // Adjust the required user level as needed

// Check if form is submitted
if (isset($_POST['submit'])) {
    $report_id = (int)$_POST['report_id'];
    $quantity = (int)$_POST['quantity'];
    $date_of_incident = $db->escape($_POST['date_of_incident']);
    $cause = $db->escape($_POST['cause']);
    $notes = $db->escape($_POST['notes']);
    $report_status = $db->escape($_POST['report_status']);

    // Handle uploaded image
    if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] === UPLOAD_ERR_OK) {
        $product_img = $_FILES['product_img'];
        $upload_result = upload_product_image($product_img);
        if ($upload_result['result'] === true) {
            // Image uploaded successfully
            $product_img_path = $upload_result['path'];
        } else {
            // Error uploading image
            $session->msg('d', $upload_result['message']);
            redirect('edit_other_damage_report.php?report_id=' . $report_id);
        }
    } else {
        // No new image uploaded, retain the existing image path
        $report = find_by_report_id('other_damage_reports', $report_id);
        $product_img_path = $report['product_img'];
    }

    // Update the report in the database
    $sql = "UPDATE other_damage_reports SET ";
    $sql .= "quantity='{$quantity}', ";
    $sql .= "date_of_incident='{$date_of_incident}', ";
    $sql .= "cause='{$cause}', ";
    $sql .= "notes='{$notes}', ";
    $sql .= "product_img='{$product_img_path}', ";
    $sql .= "report_status='{$report_status}' ";
    $sql .= "WHERE report_id='{$report_id}'";
    $result = $db->query($sql);

    if ($result && $db->affected_rows() === 1) {
        // Success
        $session->msg('s', 'Report updated successfully.');
        redirect('damage_reports.php');
    } else {
        // Failure
        $session->msg('d', 'Failed to update other damage report.');
        redirect('edit_other_damage_report.php?report_id=' . $report_id);
    }
} else {
    // Form was not submitted
    $session->msg('d', 'Form was not submitted.');
    redirect('damage_reports.php');
}
?>
