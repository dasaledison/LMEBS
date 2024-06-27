<?php
require_once('includes/load.php');
page_require_level(1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_day = $_POST['day_dropdown'];
    $status = $_POST['status_dropdown'];
    $start_time = isset($_POST['start_time']) ? $_POST['start_time'] : null;
    $end_time = isset($_POST['end_time']) ? $_POST['end_time'] : null;

    // If the status is closed, set start and end times to '0:00'
    if ($status == 'closed') {
        $start_time = '00:00';
        $end_time = '00:00';
    }

    // SQL to update or insert office hours for the selected day
    $sql = "INSERT INTO office_hours (day_of_week, start_time, end_time, set_daily_hours)
            VALUES ('$selected_day', '$start_time', '$end_time', 1)
            ON DUPLICATE KEY UPDATE start_time = '$start_time', end_time = '$end_time', set_daily_hours = 1";

    $result = $db->query($sql);

    if ($result) {
        $session->msg('success', 'Office hours updated successfully.');
        redirect('labmanager_home.php');
    } else {
        $session->msg('danger', 'Error updating office hours: ' . $db->error);
        redirect('labmanager_home.php');
    }
}
?>
