<?php
require_once('includes/load.php');

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract the borrow ID and status from the POST data
    $borrow_id = isset($_POST['borrow_id']) ? (int)$_POST['borrow_id'] : null;
    $status = isset($_POST['status']) ? $_POST['status'] : null;

    // Validate the borrow ID and status
    if ($borrow_id !== null && $status !== null) {
        // Update the borrow status in the database
        $update_result = update_borrow_status($borrow_id, $status);

        // Check if the update was successful
        if ($update_result) {
            // Return a success response
            http_response_code(200);
            echo "Borrow status updated successfully.";
            updateAllBorrowersStatus(); // Call the function to update all borrow statuses
            exit;
        } else {
            // Return an error response
            http_response_code(500);
            echo "Failed to update borrow status.";
            exit;
        }
    } else {
        // Return a bad request response if the borrow ID or status is missing
        http_response_code(400);
        echo "Bad request: Borrow ID or status is missing.";
        exit;
    }
} else {
    // Return a method not allowed response for non-POST requests
    http_response_code(405);
    echo "Method Not Allowed: Only POST requests are allowed.";
    exit;
}

// Function to update borrow status in the database
function update_borrow_status($borrow_id, $status) {
    global $db; // Assuming $db is your database connection

    // Check if the status is null
    if ($status === null) {
        // Return false indicating failure
        return false;
    }

    // Prepare and execute the update query
    $query = "UPDATE all_borrowers SET status = ? WHERE borrow_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("si", $status, $borrow_id);
    $result = $stmt->execute();

    // Return the execution result (true for success, false for failure)
    return $result;
}

// Function to update all borrow statuses in the database
function updateAllBorrowersStatus() {
    global $db; // Declare $db as global

    // Update borrow status based on borrow times
    $sql = "UPDATE all_borrowers 
            SET status = 
                CASE
                    WHEN borrowed_till < NOW() THEN 'Completed'
                    WHEN borrowed_from <= NOW() AND borrowed_till >= NOW() AND status = 'Scheduled' THEN 'In use'
                    ELSE status
                END";

    // Execute the query
    $db->query($sql);
}
?>
