<?php
require_once('includes/functions.php');
require_once('includes/load.php');
page_require_level(1); // Adjust the required user level as needed

// Check if report ID is provided via GET request
if (!isset($_GET['id'])) {
    $session->msg("d", "Report ID not provided.");
    redirect('all_other_reports.php'); // Adjust as needed
}

$id = (int)$_GET['id'];

// Find the report to be deleted
$report = find_by_report_id('lab_damage_reports', $id);

if (!$report) {
    $session->msg("d", "Report not found.");
    redirect('all_other_reports.php'); // Adjust as needed
}

// Perform the deletion
$sql = "DELETE FROM lab_damage_reports WHERE id='{$db->escape($id)}' LIMIT 1";
if ($db->query($sql)) {
    $session->msg('s', 'Report deleted successfully.');
    redirect('all_other_reports.php');
} else {
    $session->msg('d', 'Failed to delete report.');
    redirect('all_other_reports.php');
}
?>
