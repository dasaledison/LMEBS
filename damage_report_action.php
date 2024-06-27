<?php
require_once('includes/load.php');
page_require_level(1); // Requires admin level for accessing this page

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $borrower_id = $_POST['borrower_id'];
    $cause_of_damage = $_POST['cause_of_damage'];
    $notes = $_POST['notes'];
    $report_status = $_POST['report_status'];

    // Handle uploaded pictures
    $uploaded_files = $_FILES['picture'];
    $picture_filenames = [];
    if (!empty($uploaded_files['name'][0])) {
        foreach ($uploaded_files['name'] as $key => $value) {
            $file_name = $uploaded_files['name'][$key];
            $file_tmp = $uploaded_files['tmp_name'][$key];
            $file_type = $uploaded_files['type'][$key];
            $file_error = $uploaded_files['error'][$key];
            if ($file_error === 0) {
                $new_file_name = time() . '_' . $file_name;
                move_uploaded_file($file_tmp, "uploads/" . $new_file_name);
                $picture_filenames[] = $new_file_name;
            }
        }
    }

    // Generate a 6-digit random number for report_id
    $report_id = mt_rand(100000, 999999);

    // Fetch school_id from associated borrower_id
    $borrower_info = find_by_borrower_id('all_borrowers', $borrower_id);
    if ($borrower_info) {
        $school_id = $borrower_info['school_id'];
    } else {
        $session->msg('d', 'Error: Borrower information not found.');
        $previous_page = $_SERVER['HTTP_REFERER'];
        redirect($previous_page, false);
    }

    // Insert data into the database
    foreach ($picture_filenames as $picture_filename) {
        $sql = "INSERT INTO borrower_damage_report (school_id, borrower_id, picture, cause, notes, report_status, report_id) 
        VALUES ('$school_id', '$borrower_id', '$picture_filename', '$cause_of_damage', '$notes', '$report_status', '$report_id')";
        if (!$db->query($sql)) {
            $session->msg('d', 'Error: Failed to submit damage report.');
            $previous_page = $_SERVER['HTTP_REFERER'];
            redirect($previous_page, false);
        }
    }

    // Set session message and report ID
    $_SESSION['report_id'] = $report_id;
    $session->msg('s', 'Damage report submitted successfully.');

    // Redirect to view_damage_report.php with report ID
    redirect("view_damage_report.php?report_id=$report_id");
} else {
    // If form is not submitted
    $session->msg('d', 'Form not submitted.');
    $previous_page = $_SERVER['HTTP_REFERER'];
    redirect($previous_page, false);
}
?>
