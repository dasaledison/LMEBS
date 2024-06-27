<?php
require_once('includes/load.php');
// Check what level user has permission to view this page
page_require_level(1);

// Check if the school ID is provided in the URL
if (!isset($_GET['school_id'])) {
    $session->msg("d", "School ID not provided!");
    redirect_to('return_equipment.php');
}

// Retrieve the scanned school ID from the URL
$school_id = $_GET['school_id'];

// Update status to "Returned" for all borrowers with the scanned school ID
$update_result = update_status_to_returned($school_id);

if ($update_result) {
    $session->msg("s", "Status updated to Returned for all borrowers with School ID: {$school_id}");
} else {
    $session->msg("d", "Failed to update status to Returned");
}

// Redirect back to the scanning page after updating the status
redirect_to('return_equipment.php');

function update_status_to_returned($school_id) {
    global $db;

    // Update status to "Returned" for all borrowers with the specified school ID
    $sql = "UPDATE all_borrowers SET status = 'Returned' WHERE school_id = '{$school_id}'";
    $result = $db->query($sql);

    return ($result && $db->affected_rows() > 0) ? true : false;
}
?>
