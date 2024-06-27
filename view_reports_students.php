<?php
require_once('includes/functions.php');
require_once('includes/load.php');
page_require_level(2);
include_once('layouts/header.php');

// Fetch the current user
$user = current_user();

// Initialize variables
$current_user_id = 0;
$user_type = '';
$user_name = '';
$message = '';
$damaged_reports = [];

if ($user) {
    $current_user_id = intval($user['id']); // Get the user's ID
    $user_type = $user['user_type']; // Get the user's type
    $user_name = $user['name']; // Get the user's name

    if ($user_type === 'student') {
        // Fetch 'Confirmed' damaged reports for the current student user
        $status = 'Confirmed'; // Set the status to Confirmed
        $statusClass = 'status-' . strtolower(str_replace(' ', '-', $status));
        $damaged_reports = find_by_sql("SELECT * FROM student_damaged_reports WHERE user_id = {$current_user_id} AND report_status = '{$status}'");

        // Message to display based on the number of confirmed reports
        $message = ($damaged_reports) ? "You have " . count($damaged_reports) . " confirmed reports." : "Congratulations, you have no confirmed reports.";
    } else {
        // Display a message for non-student users
        $message = "You are not a student. No reports available.";
    }
} else {
    // User not found
    echo "User not found.";
    exit; // Exit the script
}
?>

<style>
    /* Add your styles here */
</style>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
        
        <div class="alert alert-info"><?php echo $message; ?></div>

        <?php if ($damaged_reports) : ?>
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <span class="<?php echo $statusClass; ?>"><?php echo $status; ?> Damaged Reports</span>
                    </strong>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead style="background-color: <?php echo $statusClass; ?>;">
                                <tr>
                                    <th>Image</th>
                                    <th>Quantity</th>
                                    <th>Date of Incident</th>
                                    <th>Cause</th>
                                    <th>Equipment</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($damaged_reports as $report) : ?>
                                    <tr>
                                        <td>
                                            <a href="#" data-toggle="modal" data-target="#imageModal<?php echo $report['id']; ?>">
                                                <img src="<?php echo $report['product_img']; ?>" class="img-thumbnail">
                                            </a>

                                            <!-- Modal to display a larger image -->
                                            <div class="modal fade" id="imageModal<?php echo $report['id']; ?>" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Image</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <img src="<?php echo $report['product_img']; ?>" class="img-responsive">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $report['quantity']; ?></td>
                                        <td><?php echo $report['date_of_incident']; ?></td>
                                        <td><?php echo $report['cause']; ?></td>
                                        <td><?php echo $report['equipment']; ?></td>
                                        <td><?php echo $report['notes']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript" src="libs/js/functions.js"></script>

<?php include_once('layouts/footer.php'); ?>
