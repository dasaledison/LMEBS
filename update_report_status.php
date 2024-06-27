<?php
require_once('includes/load.php');
page_require_level(1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate POST data
    $report_id = isset($_POST['report_id']) ? $_POST['report_id'] : null;
    $resolve_notes = isset($_POST['resolve_notes']) ? $_POST['resolve_notes'] : '';

    if (!$report_id) {
        // Handle invalid data
        $session->msg('d', 'Invalid Report ID.');
        redirect("all_other_reports.php");   
        exit;
    }

    // Update report status to Resolved and insert resolve notes
    $sql_update = "UPDATE borrower_damage_report SET report_status = 'Resolved', resolve_notes = '{$resolve_notes}' WHERE id = {$report_id}";
    $result = $db->query($sql_update);

    if ($result) {
        // Success message
        $session->msg('s', 'Report status updated successfully to Resolved.');
        $previous_page = $_SERVER['HTTP_REFERER'];
        redirect($previous_page, false); 
    } else {
        // Handle update failure
        $session->msg('d', 'Error updating report status.');
        $previous_page = $_SERVER['HTTP_REFERER'];
        redirect($previous_page, false); 
    }
} else {
    // Handle invalid request method
    echo "Invalid request method.";
    $session->msg('d', 'Invalid request method.');
    $previous_page = $_SERVER['HTTP_REFERER'];
    redirect($previous_page, false); 
}
?>
