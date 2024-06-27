<?php
require_once('includes/functions.php');
require_once('includes/load.php');
page_require_level(1); // Adjust the required user level as needed

// Check if the form is submitted
if (isset($_POST['submit'])) {
    $report_id = (int)$_POST['report_id'];
    $quantity = $_POST['quantity'];
    $cause = $_POST['cause'];
    $notes = $_POST['notes'];
    $report_status = $_POST['report_status'];
    $student_id = $_POST['student_id'];


    if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] === UPLOAD_ERR_OK) {
        $product_img = $_FILES['product_img'];
        $upload_result = upload_product_image($product_img);
        if ($upload_result['result'] === true) {
            // Image uploaded successfully
            $product_img_path = $upload_result['path'];
        } else {
            // Error uploading image
            $session->msg('d', $upload_result['message']);
            redirect('edit_student_damage_report.php?report_id=' . $report_id);
        }
    } else {
        // No new image uploaded, retain the existing image path
        $report = find_by_report_id('student_damage_reports', $report_id);
        $product_img_path = $report['product_img'];
    }

            // Update the database record with the new information
            $sql = "UPDATE student_damage_reports SET ";
            $sql .= "quantity='{$db->escape($quantity)}', ";
            $sql .= "cause='{$db->escape($cause)}', ";
            $sql .= "notes='{$db->escape($notes)}', ";
            $sql .= "report_status='{$db->escape($report_status)}', ";
            $sql .= "student_id='{$db->escape($student_id)}', ";
            $sql .= "product_img='{$db->escape($image_path)}' ";
            $sql .= "WHERE report_id='{$db->escape($report_id)}'";

            if ($db->query($sql)) {
                // Now trigger the update in the products table
                $update_products_sql = "UPDATE products SET quantity = quantity - {$db->escape($quantity)} WHERE asset_num = (SELECT asset_num FROM student_damage_reports WHERE report_id = {$db->escape($report_id)})";
                $db->query($update_products_sql);

                $session->msg('s', 'Report updated successfully.');
                redirect('damage_reports.php');
            } else {
                $session->msg('d', 'Failed to update report.');
                redirect('edit_student_damage_report.php?report_id=' . $report_id);
            }
        } else {
            $session->msg('d', "Sorry, there was an error uploading your file.");
            redirect('desired_page.php'); // Adjust as needed
        }

?>
