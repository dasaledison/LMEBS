<?php
// Include necessary files and start session if required
require_once('includes/load.php');
// Check user's permission level
page_require_level(1);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if necessary fields are set
    if (isset($_POST['borrower_id'])) {
        // Retrieve borrower ID
        $borrower_id = intval($_POST['borrower_id']); // Sanitize input
        
        function update_borrower_details($borrower_id, $db) {
            $returned_time = date('Y-m-d H:i:s');
            $update_time_sql = "UPDATE all_borrowers SET returned_time = '{$returned_time}', status = 'Returned' WHERE borrower_id = {$borrower_id}";
            $db->query($update_time_sql);
        
            // Update good_condition and bad_condition for each item
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'good_condition_') === 0 || strpos($key, 'bad_condition_') === 0) {
                    $asset_num = substr($key, strrpos($key, '_') + 1); // Extract asset number from input key
                    $condition_type = strpos($key, 'good_condition_') === 0 ? 'good_condition' : 'bad_condition';
                    $update_condition_sql = "UPDATE all_borrowers SET {$condition_type} = {$value} WHERE borrower_id = {$borrower_id} AND asset_num = '{$asset_num}'";
                    $db->query($update_condition_sql);
                }
            }
        }

        // Validate quantities for each item
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'good_condition_') === 0 || strpos($key, 'bad_condition_') === 0) {
                $asset_num = substr($key, strrpos($key, '_') + 1); // Corrected to strrpos
                $condition_type = strpos($key, 'good_condition_') === 0 ? 'good_condition' : 'bad_condition';
                
                // Retrieve quantity for this item
                $quantity = get_quantity_for_item($asset_num);

                // Ensure the value is numeric
                if (!is_numeric($value)) {
                    // Handle validation error
                    $session->msg('d', 'Invalid quantity. Please input a number.');
                    $previous_page = $_SERVER['HTTP_REFERER'];
                    redirect($previous_page, false);   
                }

                // Calculate total quantity of good and bad conditions based on quantity
                $totalConditions = intval($_POST['good_condition_' . $asset_num]) + intval($_POST['bad_condition_' . $asset_num]);

                // Check if the total quantity of good and bad conditions equals the available quantity
                if ($totalConditions != $quantity) {
                    // Display a warning message
                    $session->msg('w', 'Total quantity of good and bad conditions should be equal to the available quantity.');
                    $previous_page = $_SERVER['HTTP_REFERER'];
                    redirect($previous_page, false);
                }
            }
        }

        // Update borrower details
        update_borrower_details($borrower_id, $db);

        // Redirect to a success page or do further processing
        // header("Location: success.php");
        // exit();

        // For demonstration purposes, just print a success message
        // Check if all bad conditions are zero
        $allBadZero = true;
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'bad_condition_') === 0 && $value != 0) {
                $allBadZero = false;
                break;
            }
        }

        // Redirect based on bad condition values
        if ($allBadZero) {
            // All bad conditions are zero, redirect to return_success.php with the borrower_id
            redirect("return_success.php?borrower_id=$borrower_id");
        } else {
            // Bad conditions exist, redirect to damage_report_process.php with the borrower_id
            redirect("damage_report_process.php?borrower_id=$borrower_id");
        }
    } else {
        // If borrower ID is not provided
        echo json_encode(array('error' => 'Borrower ID is missing.'));
    }
} else {
    // If form is not submitted
    echo json_encode(array('error' => 'Form not submitted.'));
}
?>
