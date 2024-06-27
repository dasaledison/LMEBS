<?php
require_once('includes/load.php');
require_once('includes/sql.php');
updateReservationStatus(); // Call the function to update reservation status

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract the reservation ID and status from the POST data
    $reservation_id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $status = isset($_POST['status']) ? $_POST['status'] : null;

    // Validate the reservation ID and status
    if ($reservation_id !== null && $status !== null) {
        // Check if the status is 'Cancelled'
        if ($status === 'Cancelled') {
            // Update the reservation status in the database
            $update_result = update_reservation_status($reservation_id, $status);

            // Check if the update was successful
            if ($update_result) {
                // Return a success response
                http_response_code(200);
                echo "Reservation status updated successfully.";
                exit;
            } else {
                // Return an error response
                http_response_code(500);
                echo "Failed to update reservation status.";
                exit;
            }
        } else {
            // Return a bad request response for invalid status
            http_response_code(400);
            echo "Bad request: Invalid status for cancellation.";
            exit;
        }
    } else {
        // Return a bad request response if the reservation ID or status is missing
        http_response_code(400);
        echo "Bad request: Reservation ID or status is missing.";
        exit;
    }
} else {
    // Return a method not allowed response for non-POST requests
    http_response_code(405);
    echo "Method Not Allowed: Only POST requests are allowed.";
    exit;
}

// Function to update reservation status in the database
function update_reservation_status($reservation_id, $status) {
    global $db; // Assuming $db is your database connection

    $query = "UPDATE student_reserved SET reservation_status = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("si", $status, $reservation_id);
    $result = $stmt->execute();

    return $result;
}
?>
