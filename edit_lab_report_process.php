<?php
require_once('includes/load.php');
page_require_level(1); // Requires admin level for accessing this page

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $cause = isset($_POST['cause']) ? $db->escape($_POST['cause']) : '';
    $notes = isset($_POST['notes']) ? $db->escape($_POST['notes']) : '';
    $asset_num = isset($_POST['asset_num']) ? $db->escape($_POST['asset_num']) : '';
    $quantity = isset($_POST['quantity']) ? (int)$db->escape($_POST['quantity']) : 0;

    // Get the report ID from the form
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;

    // Check if new file is uploaded
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['picture']['tmp_name'];
        $file_name = $_FILES['picture']['name'];
        $file_error = $_FILES['picture']['error'];
        
        // Handle uploaded picture
        if ($file_error === 0) {
            $new_file_name = time() . '_' . $file_name;
            $destination = "uploads/" . $new_file_name;
            if (move_uploaded_file($file_tmp, $destination)) {
                // If new picture is uploaded, use the new file name
                $picture_filename = $new_file_name;
            } else {
                $session->msg('d', "Error: Failed to upload $file_name.");
                redirect('all_other_reports.php');
            }
        }
    } else {
        // No new file uploaded, retain the old file name
        $sql = "SELECT picture FROM lab_damage_reports WHERE id = '{$db->escape($id)}' LIMIT 1";
        $result = $db->query($sql);
        if ($db->num_rows($result) === 1) {
            $row = $db->fetch_assoc($result);
            $picture_filename = $row['picture'];
        } else {
            $session->msg('d', 'Error: Failed to retrieve existing picture.');
            redirect('all_other_reports.php');
        }
    }

    // Update data in the database
    $sql = "UPDATE lab_damage_reports 
            SET picture = '$picture_filename', 
                cause = '$cause', 
                notes = '$notes', 
                asset_num = '$asset_num', 
                quantity = '$quantity' 
            WHERE id = $id";
    if (!$db->query($sql)) {
        $session->msg('d', 'Error: Failed to update damage report.');
        redirect('edit_lab_report.php?id=' . $id);
    }

    // Fetch the report_id associated with the provided id
    $sql_report_id = "SELECT report_id FROM lab_damage_reports WHERE id = '{$db->escape($id)}' LIMIT 1";
    $report_id_result = $db->query($sql_report_id);

    if ($db->num_rows($report_id_result) === 1) {
        $report_id_row = $db->fetch_assoc($report_id_result);
        $report_id = $report_id_row['report_id'];
    } else {
        $session->msg('d', 'Error: Failed to retrieve report ID.');
        redirect('all_other_reports.php');
    }

    // Set session message
    $session->msg('s', 'Damage report updated successfully.');

    // Redirect to view_lab_report.php with report_id
    redirect("view_lab_report.php?report_id=$report_id");
} else {
    // If form is not submitted
    $session->msg('d', 'Form not submitted.');
    redirect('all_other_reports.php');
}
?>
