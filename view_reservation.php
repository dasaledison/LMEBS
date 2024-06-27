<?php
require_once('includes/functions.php');
require_once('includes/load.php');
page_require_level(2);
include_once('layouts/header.php');

// Fetch the current user
$user = current_user();

if ($user) {
    $current_user_id = intval($user['id']); // Get the user's ID
} else {
    // User not found
    echo "User not found.";
    exit; // Exit the script
}
$reservations_pending = find_by_sql("SELECT * FROM student_reserved WHERE user_id = {$current_user_id} AND reservation_status = 'Pending'");
$reservations_approved = find_by_sql("SELECT * FROM student_reserved WHERE user_id = {$current_user_id} AND reservation_status = 'Approved'");
$reservations_in_use = find_by_sql("SELECT * FROM student_reserved WHERE user_id = {$current_user_id} AND reservation_status = 'In Use'");

$reservations_pending_count = count($reservations_pending);
$reservations_approved_count = count($reservations_approved);
$reservations_in_use_count = count($reservations_in_use);
?>


<style>
    /* Conditional styling for reservation status */
    .status-pending {
        background-color: #FFA500; /* Orange */
        color: white;
        padding: 5px;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .status-approved {
        background-color: #4CAF50; /* Green */
        color: white;
        padding: 5px;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .status-in-use {
        background-color: #3498DB; /* Blue */
        color: white;
        padding: 5px;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .status-completed {
        background-color: #808080; /* Dark Grey */
        color: white;
        padding: 5px;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .status-cancelled {
        background-color: #FF0000; /* Red */
        color: white;
        padding: 5px;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .img-thumbnail1 {
        height: 50px;
        width: 50px;
        vertical-align: middle;
    }

    /* Smaller fonts */
    .panel-heading h3 {
        font-size: 18px;
    }

    table {
        font-size: 14px;
    }

    th {
        padding: 8px;
        color: black; /* Text color for table headers */
    }

    td {
        padding: 8px;
    }

    /* Icon styles */
    .action-icons {
        font-size: 18px;
        margin-right: 5px;
    }

    .approve-icon {
        color: green;
    }

    .cancel-icon {
        color: red;
    }

    .action-label {
        font-size: 14px;
        margin-left: 5px;
    }

    .btn1 {
        font-size: 12px;
        padding: 3px;
    }
</style>

<div class="row">
    <div class="col-md-12">
    <div class="alert alert-info">
            You have <?php echo $reservations_pending_count; ?> pending reservations,
            <?php echo $reservations_approved_count; ?> approved reservations, and
            <?php echo $reservations_in_use_count; ?> in-use reservations.
        </div>

       
        <?php echo display_msg($msg); ?>

        <?php
        $statuses = array('Pending', 'In Use', 'Approved', 'Completed', 'Cancelled');
        foreach ($statuses as $status) :
            $statusClass = 'status-' . strtolower(str_replace(' ', '-', $status));
            ?>

            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <span class="<?php echo $statusClass; ?>"><?php echo $status; ?> Items</span>
                    </strong>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead style="background-color: <?php echo $statusClass; ?>;">
                                <tr>
                                    <th>Image</th>
                                    <th>Equipment</th>
                                    <th>Qty</th>
                                    <th>From</th>
                                    <th>Till</th>
                                    <th>Group Number</th>
                                    <th>Student Name</th>
                                    <th>Student ID</th>
                                    <th>Section</th>
                                    <?php if ($status == 'Pending' || $status == 'Approved') : ?>
                                        <th>Actions</th>
                                    <?php endif; ?>
                                
                                        <?php if ($status == 'Cancelled') : ?>
                                       <th>Reason of Cancellation</th>
                                  <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch reservations for the current user
                                $reservations = find_by_sql("SELECT * FROM student_reserved WHERE user_id = {$current_user_id} AND reservation_status = '{$status}'");
                                foreach ($reservations as $reservation) :
                                    ?>
                                    <tr>
                                        <td><img src="uploads/products/<?php echo $reservation['product_img']; ?>" class="img-thumbnail1" /></td>
                                        <td><?php echo $reservation['equipment']; ?></td>
                                        <td><?php echo $reservation['quantity']; ?></td>
                                        <td><?php echo date('Y-m-d h:i A', strtotime($reservation['reservation_date_from'])); ?></td>
                                        <td><?php echo date('Y-m-d h:i A', strtotime($reservation['reservation_date_till'])); ?></td>
                                        <td><?php echo $reservation['group_number']; ?></td>
                                        <td><?php echo $reservation['student_name']; ?></td>
                                        <td><?php echo $reservation['student_id']; ?></td>
                                        <td><?php echo $reservation['section']; ?></td>
                                        <?php if ($status == 'Cancelled') : ?>
                <td><?php echo $reservation['cancel_notes']; ?></td>
            <?php endif; ?>
                                        <td>
                                           

                                            <?php if ($status == 'Approved' || $status == 'Pending') : ?>
                                                <a href="admin_cancel_reservation_students.php?id=<?php echo $reservation['id']; ?>" class="btn btn-danger btn-sm m-4" onclick="return confirm('Are you sure you want to cancel this reservation?')">
                                                    Cancel <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script type="text/javascript" src="libs/js/functions.js"></script>

<?php include_once('layouts/footer.php'); ?>