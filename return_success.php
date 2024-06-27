<?php
require_once('includes/load.php');
require_once('includes/sql.php');
$page_title = "Return Success";
include_once('layouts/header.php');

// Retrieve borrower_id from URL parameter
if (isset($_GET['borrower_id'])) {
    $borrower_id = $_GET['borrower_id'];

    // Fetch borrower details
    $borrower_details = find_by_borrower_id('all_borrowers', $borrower_id);

    // Check if borrower details exist
    if ($borrower_details) {
        // Fetch borrow details for the specific borrower
        $borrow_details = find_borrow_details_by_borrower_id($borrower_id);
        // Check if borrow details exist
        if ($borrow_details) {
            // Display the success message and borrow details
?>
            <div class="container mt-5">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">Return Successful!</h4>
                    <p>The return details have been successfully submitted.</p>
                    <hr>
                    <p>Borrower Details:</p>
                    <ul>
                        <li><strong>Borrower ID:</strong> <?php echo $borrower_details['borrower_id']; ?></li>
                        <li><strong>School ID:</strong> <?php echo $borrower_details['school_id']; ?></li>
                        <li><strong>Borrowed From:</strong> <?php echo $borrow_details['borrowed_from']; ?></li>
                        <li><strong>Returned Time:</strong> <?php echo $borrow_details['returned_time']; ?></li>
                        <li><strong>Subject ID:</strong> <?php echo $borrow_details['subject_id']; ?></li>
                        <li><strong>Asset Number:</strong> <?php echo $borrow_details['asset_num']; ?></li>
                        <li><strong>Returned Quantity:</strong> <?php echo $borrow_details['good_condition']; ?></li>
                        <li><strong>Status:</strong> <?php echo $borrow_details['status']; ?></li>
                    </ul><br><br>
                    <a class="btn btn-primary" href="return_equipment.php"> Return Another Item</a>
                </div>
            </div>
            
<?php
        } else {
            // If borrow details do not exist
            $session->msg('d', 'Error: Borrow details not found.');
            redirect('borrow_equipment.php'); // Redirect to borrow_equipment.php or any other appropriate page
        }
    } else {
        // If borrower details do not exist
        $session->msg('d', 'Error: Borrower details not found.');
        redirect('borrow_equipment.php'); // Redirect to borrow_equipment.php or any other appropriate page
    }
} else {
    // If borrower_id is not provided in the URL
    $session->msg('d', 'Error: Borrower ID is missing.');
    redirect('borrow_equipment.php'); // Redirect to borrow_equipment.php or any other appropriate page
}

// Include the footer
include_once('layouts/footer.php');
?>
