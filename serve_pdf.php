<?php
// Get the filename from the query string
$filename = isset($_GET['filename']) ? $_GET['filename'] : '';

// Check if the filename is valid
if (!empty($filename) && preg_match('/^top_used_equipments_report_\d{14}\.pdf$/', $filename)) {
    // Set headers to force download
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');

    // Output the PDF file
    readfile(sys_get_temp_dir() . '/' . $filename);
} else {
    // Invalid filename, handle error (e.g., redirect to an error page)
    header('Location: error.php');
    exit;
}
?>
