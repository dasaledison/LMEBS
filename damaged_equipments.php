<?php
require_once('includes/functions.php');
require_once('includes/load.php');
page_require_level(1);
include_once('layouts/header.php');

// Function to get user type from the database based on user_id
function getUserType($userId) {
    global $db;
    $userTypeQuery = "SELECT user_type FROM users WHERE id = '{$userId}' ";
    $result = $db->query($userTypeQuery);
    $userType = $db->fetch_assoc($result);

    // Check if the key 'user_type' exists in the array
    return isset($userType['user_type']) ? $userType['user_type'] : 'N/A';
}

function getUserLevel($userId) {
    global $db;
    $userLevelQuery = "SELECT user_level FROM users WHERE id = '{$userId}' ";
    $result = $db->query($userLevelQuery);
    $userLevel = $db->fetch_assoc($result);

    // Check if the key 'user_level' exists in the array
    return isset($userLevel['user_level']) ? $userLevel['user_level'] : 'N/A';
}

// Fetch all damaged reports
$student_damaged_reports = find_all('student_damaged_reports');
$teachers_damaged_reports = find_all('teachers_damaged_reports');

// Combine both student and teacher reports
$all_damaged_reports = array_merge($student_damaged_reports, $teachers_damaged_reports);

// Initialize the array if it's not set
$all_damaged_reports = isset($all_damaged_reports) ? $all_damaged_reports : [];

// Filter parameters
$userType = isset($_POST['user_type']) ? $_POST['user_type'] : '';
$dateFrom = isset($_POST['date_from']) ? $_POST['date_from'] : '';
$dateTill = isset($_POST['date_till']) ? $_POST['date_till'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$equipment = isset($_POST['equipment']) ? $_POST['equipment'] : '';
$id = isset($_POST['id']) ? $_POST['id'] : '';
$userLevel = isset($_POST['user_level']) ? $_POST['user_level'] : '';

// Filter reports based on input values
$filtered_reports = array_filter($all_damaged_reports, function ($report) use ($userType, $dateFrom, $dateTill, $name, $equipment, $id, $userLevel) {
    $reportDate = isset($report['date_of_incident']) ? new DateTime($report['date_of_incident']) : null;

    return (
        (empty($userType) || (isset($report['user_type']) && $report['user_type'] === ucfirst(strtolower($userType)))) &&
        (empty($userLevel) || (isset($report['user_level']) && $report['user_level'] == $userLevel)) &&
        (empty($dateFrom) || ($reportDate && $reportDate >= new DateTime($dateFrom))) &&
        (empty($dateTill) || ($reportDate && $reportDate <= new DateTime($dateTill))) &&
        (empty($name) || (
            (isset($report['user_type']) && $report['user_type'] === 'Student' && isset($report['student_name']) && stripos($report['student_name'], $name) !== false) ||
            (isset($report['user_type']) && $report['user_type'] === 'Teacher' && isset($report['teacher_name']) && stripos($report['teacher_name'], $name) !== false)
        )) &&
        (empty($equipment) || (isset($report['equipment']) && stripos($report['equipment'], $equipment) !== false)) &&
        (empty($id) || (
            (isset($report['user_type']) && $report['user_type'] === 'Student' && isset($report['student_id']) && $report['student_id'] == $id) ||
            (isset($report['user_type']) && $report['user_type'] === 'Teacher' && isset($report['employee_id']) && $report['employee_id'] == $id)
        ))
    );
});
?>

<style>
    /* Conditional styling for report status */
    .status-pending {
        background-color: #FFA500; /* Orange */
        color: white;
        padding: 5px;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .status-confirmed {
        background-color: #4CAF50; /* Green */
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

    .fixed-header {
        top: -1px;
        background-color: #f2f2f2;
        z-index: 1000;
    }

    .scrollable-container {
        max-height: 500px; /* Adjust the max-height as needed */
        overflow-y: auto;
    }

    .white-box {
        background-color: white;
        padding: 15px;
        border-radius: 5px;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    /* Style for status buttons */
    .status-btn {
        float: left;
        display: block;
        color: #969595; /* Change color as needed */
        text-align: center;
        padding: 10px;
        text-decoration: none;
        font-size: 14px;
        background-color: transparent; /* Remove background color */
        border: none; /* Remove border */
        border-radius: 0px;
        margin-bottom: 0px;
    }

    /* Hover effect for status buttons */
    .status-btn:hover {
        border-bottom: 3px solid #80BCBD; /* Change color as needed */
    }

    /* Permanent underline for clicked status button */
    .status-btn.active {
        border-bottom: 3px solid #80BCBD; /* Change color as needed */
        color: #333; /* Change color as needed */
        font-weight: bold; /* Add this line to make the text bold */
    }
    .larger-icon {
        font-size: 3em; /* Adjust the size as needed */
    }
</style>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-9">
        <?php
        $statuses = array('Under Review', 'Confirmed', 'Cancelled');
        foreach ($statuses as $status) :
            $statusLabel = ($status == 'Under Review') ? 'Under Review' : $status;
            $statusClass = ($status == 'Under Review') ? 'status-pending' : 'status-' . strtolower(str_replace(' ', '-', $status));
            ?>

            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <strong>
                        <span class="glyphicon glyphicon-th"></span>
                        <span class="<?php echo $statusClass; ?>"><?php echo $statusLabel; ?> Reports</span>
                    </strong>
                </div>
                <div class="panel-body">
                    <div class="table-responsive scrollable-container">
                        <table class="table table-striped">
                            <thead class="fixed-header" style="background-color: <?php echo $statusClass; ?>;">
                                <tr>
                                    <th>Image</th>
                                    <th>Equipment</th>s
                                    <th>Quantity</th>
                                    <th>Date of Incident</th>
                                    <th>Cause</th>
                                    
                                    <th>Notes</th>
                                    <?php if ($status == 'Under Review') : ?>
                                        <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($filtered_reports as $report) :
                                    if ($report['report_status'] == $status) :
                                ?>
                                            <tr>
                                                <td>
                                                    <!-- Make the image clickable and open in a modal -->
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
                                                <td><?php echo $report['equipment']; ?></td>
                                                <td><?php echo $report['quantity']; ?></td>
                                                <td><?php echo $report['date_of_incident']; ?></td>
                                                <td><?php echo $report['cause']; ?></td>
                                              <!-- <td>
                                                    <?php
                                                    if ($userLevel == 2 ) {
                                                        echo isset($_GET['student_id']) ? $_GET['student_id'] : '';
                                                    } elseif ($userLevel == 3) {
                                                        echo isset($_GET['employee_id']) ? $_GET['employee_id'] : '';
                                                    } else {
                                                        echo '';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ( getUserType($report['user_id']) === 2) {
                                                        echo isset($report['student_name']) ? $report['student_name'] : '';
                                                    } elseif (getUserType($report['user_id'])=== 3) {
                                                        echo isset($report['teacher_name']) ? $report['teacher_name'] : '';
                                                    } else {
                                                        echo '';
                                                    }
                                                    ?>
                                                </td>--> 
                                                <td><?php echo $report['notes']; ?></td>
                                                <td>
                                                    <?php if ($status == 'Under Review') : ?>
                                                        <a href="confirm_damage.php?id=<?php echo $report['id']; ?>" class="btn btn-success btn-confirm btn-sm m-4" onclick="return confirm('Are you sure you want to confirm this damaged report?')">Confirm</a>
                                                        <a href="cancel_damage.php?id=<?php echo $report['id']; ?>" class="btn btn-danger btn-cancel btn-sm m-4" onclick="return confirm('Are you sure you want to cancel this damaged report?')">Cancel</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="col-md-3">
        <div class="white-box text-center">
            <span class="glyphicon glyphicon-pencil larger-icon glyphicon-4x mb-4"></span>
            <h3 class="mb-4">Submit a Report</h3>
            <button class="btn btn-primary mb-2" onclick="location.href='damaged_equipments_students.php'">Student</button>
            <button class="btn btn-primary" onclick="location.href='damaged_equipments_teachers.php'">Teacher</button>
        </div>
    </div>

    <div class="col-md-3">
        <div class="white-box">
            <div class="btn-group" role="group" aria-label="Report Status">
                <?php
                // Add "All" button first and initially clicked
                echo '<button type="button" class="status-btn active" onclick="filterReports(\'All\')">All</button>';

                // Generate other status buttons
                $statuses = array('Under Review', 'Confirmed', 'Cancelled');
                foreach ($statuses as $status) :
                    $statusClass = 'status-' . strtolower(str_replace(' ', '-', $status));
                ?>
                        <button type="button" class="status-btn <?php echo $statusClass; ?>" onclick="filterReports('<?php echo $status; ?>')"><?php echo $status; ?></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Filter options
    <div class="col-md-3 filter-box">
        <div class="draggable-panel panel panel-default">
            <div class="panel-heading">
                <strong>Filter Options</strong>
                <span class=""><i class="fas fa-arrows-alt"></i></span>
            </div>
            <div class="panel-body">
                <form action="" method="post">
                    <div class="form-group">
                        <label for="user_type_filter">User Type:</label>
                        <select id="user_type_filter" name="user_type" class="form-control">
                            <option value="">All</option>
                            <option value="Student">Student</option>
                            <option value="Teacher">Teacher</option>
                        </select>
                    </div>
           
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </form>
            </div>
        </div>
    </div>-->
    <div class="col-md-3 print-box">
        <div class="draggable-panel panel panel-default">
            <div class="panel-heading">
                <strong>Print Options</strong>
                <span class=""><i class="fas fa-arrows-alt"></i></span>
            </div>
            <div class="panel-body">
    <!-- Print options -->

    <a href="generate_pdf_all_confirmed_reports.php?report=true" class="btn btn-warning btn-block">All Confirmed</a>
</div>
        </div>
    </div>
</div>
</div>

<script type="text/javascript" src="libs/js/functions.js"></script>
<script>
    $(document).ready(function () {
        // Show all tables initially
        filterReports('All');
    });

    function filterReports(status) {
        // Hide all panels first
        $('.panel').hide();

        // Show the panel corresponding to the selected status
        $('.panel').each(function () {
            var panelStatus = $(this).find('.panel-heading strong span').text().trim();
            var reportStatus = (status === 'All') ? 'Reports' : status + ' Reports';

            if (status === 'All' || panelStatus === reportStatus) {
                $(this).show();
            }
        });

        // Adjust the link based on the selected status
        var statusLink = (status === 'All') ? '' : '?report_status=' + status.toLowerCase();
        $('.status-btn').each(function () {
            $(this).attr('onclick', "filterReports('" + $(this).text().trim() + "')");
        });
        $('.status-btn').removeClass('active');
        $('.status-btn:contains("' + status + '")').addClass('active');
    }
</script>

<?php include_once('layouts/footer.php'); ?>