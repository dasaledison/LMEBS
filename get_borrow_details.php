<?php
// Include necessary files
require_once('includes/load.php');

// Check if school_id is provided
if (isset($_GET['school_id'])) {
    // Retrieve school_id from the URL
    $school_id = $db->escape($_GET['school_id']);
    
    // Initialize response array
    $response = array();
    
    // Query to retrieve borrower ID based on school ID
    $sql = "SELECT borrower_id FROM all_borrowers WHERE school_id = '{$school_id}'";
    $result = $db->query($sql);
    
    if ($borrower = $db->fetch_assoc($result)) {
        // Borrower ID found, add it to response
        $response['borrower_id'] = $borrower['borrower_id'];
        
        // Query to retrieve equipment details for the borrower with status 'In Use'
        $sql = "SELECT asset_num, quantity FROM all_borrowers WHERE school_id = '{$school_id}' AND status = 'In Use' AND borrower_id = '{$borrower['borrower_id']}'";
        $result = $db->query($sql);
        
        $equipment = array();
        while ($row = $db->fetch_assoc($result)) {
            $equipment[] = $row;
        }
        // Add equipment details to response
        $response['equipment'] = $equipment;
    } else {
        // Borrower not found, return error message
        $response['borrower_id'] = "Borrower not found";
        $response['equipment'] = array();
    }
    
    // Return JSON response
    echo json_encode($response);
} else {
    // If school_id is not provided, return error
    echo json_encode(array('error' => 'School ID not provided'));
}
?>
