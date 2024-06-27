<?php
require_once('includes/functions.php');
require_once('includes/load.php');
page_require_level(1);
include_once('layouts/header.php');

// Initialize variables
$search_term = '';
$results = array();

// Function to count reports by status
function count_reports_by_status($reports, $status) {
    $count = 0;
    foreach ($reports as $report) {
        if ($report['report_status'] == $status) {
            $count++;
        }
    }
    return $count;
}

// Check if search form is submitted
if (isset($_POST['search'])) {
    // Get search terms
    $school_id = $_POST['school_id'];
    $subject_id = $_POST['subject_id'];
    $asset_number = $_POST['asset_number'];

    // Filter damage reports based on the search terms
    $student_damage_reports = search_damage_reports($school_id, $subject_id, $asset_number, 'student');
    $other_damage_reports = search_damage_reports($school_id, $subject_id, $asset_number, 'other');
} else {
    // Get all damage reports from the database
    $student_damage_reports = find_all_student_damage_reports();
    $other_damage_reports = find_all_other_damage_reports_grouped_by_cause();

    // Separate other damage reports based on cause
    $other_reports_by_cause = array();

    foreach ($other_damage_reports as $report) {
        $cause = $report['cause'];
        $other_reports_by_cause[$cause][] = $report;
    }
}
?>

<style>
  .panel-heading strong {
    font-size: 16px;
}

.table {
    font-size: 12px;
}

th,
td {
    padding: 8px;
}

/* Status colors and box styling */
.status-orange {
    background-color: orange;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    display: inline-block;
}

.status-green {
    background-color: green;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    display: inline-block;
}

.status-red {
    background-color: red;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    display: inline-block;
}
.status-blue {
    background-color: blue;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    display: inline-block;
}
</style>
<div class="row">
<?php echo display_msg($msg); ?>

    <!-- Filter box -->
    <div class="col-md-3 filter-box">
        <div class="draggable-panel panel">
        
            <div class="panel-body">
                <!-- Report type selection form -->
                <form id="reportTypeForm" action="#" method="post">
                    <div class="form-group">
                        <label for="report_type">Create Report:</label>
                        <select id="report_type" name="report_type" class="form-control">
                            <option value="student">Student Damage Report</option>
                            <option value="other">Other Damage Report</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="create_report" class="btn btn-sm mt-2 btn-primary">Proceed</button>
                    </div>
                </form>
                <!-- End of report type selection form -->
            </div>
        </div>
    </div>
    <!-- End of Filter box -->

    <!-- Boxes showing the count of reports -->
 
        <div class="row">
            <!-- Box for Student Damage Reports count -->
            <div class="col-md-2">
                <div class="panel ">
                    <div class="panel-heading">
                        <strong>Student Reports</strong>
                    </div>
                    <div class="panel-body">
                        <h4><?php echo count($student_damage_reports); ?></h4>
                    </div>
                </div>
            </div>

            <!-- Box for Other Damage Reports count -->
            <div class="col-md-2">
                <div class="panel ">
                    <div class="panel-heading">
                        <strong>Other Reports</strong>
                    </div>
                    <div class="panel-body">
                        <h4><?php echo count($other_damage_reports); ?></h4>
                    </div>
                </div>
            </div>

            <!-- Box for report status count -->
            <div class="col-md-2">
                <div class="panel">
                    <div class="panel-heading">
                        <strong>Report Status</strong>
                    </div>
                    <div class="panel-body">
                      
                            <strong>Under Review:</strong> <?php echo count_reports_by_status($student_damage_reports, 'Under Review') + count_reports_by_status($other_damage_reports, 'Under Review'); ?></li>
<br><strong>Confirmed:</strong> <?php echo count_reports_by_status($student_damage_reports, 'Confirmed') + count_reports_by_status($other_damage_reports, 'Confirmed'); ?></li>
<br>    <strong>Resolved:</strong> <?php echo count_reports_by_status($other_damage_reports, 'Resolved'); ?></li>
                        
                    </div>
                </div>
            </div>
            <div class="col-md-2 print-box">
    <div class="draggable-panel panel panel-default">
        <div class="panel-heading">
            <strong>Print Options</strong>
            <span class=""><i class="fas fa-arrows-alt"></i></span>
        </div>
        <div class="panel-body">
            <!-- Print options dropdown -->
            <div class="dropdown">
                <button class="btn btn-info btn-primary btn-sm dropdown-toggle" type="button" id="printOptionsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Print Options
                </button>
                <div class="dropdown-menu" aria-labelledby="printOptionsDropdown">
                    <a class="dropdown-item" href="generate_pdf_reports_under_review.php?report=under_review">Under Review</a><br>
                    <a class="dropdown-item" href="generate_pdf_reports_confirmed.php?report=Confirmed">Confirmed</a><br>
                    <a class="dropdown-item" href="generate_pdf_reports_resolved.php?report=Resolved">Resolved</a>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- End of boxes showing the count of reports -->

    <div class="col-md-12">
        <!-- Student Damage Reports section -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>Student Damage Reports</strong>
            </div>
            <div class="panel-body">
            <div style="max-height: 300px; overflow: auto;">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>report_id</th>
                            <th>product_img</th>
                            <th>product_name</th>
                            <th>Qty</th>
                            <th>date_of_incident</th>
                            <th>cause</th>
                            <th>notes</th>
                            <th>report_status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($student_damage_reports as $report): ?>
                            <tr>
                                <td><?php echo $report['report_id']; ?></td>
                                <td>
                                    <a href="#" data-toggle="modal" data-target="#imageModal<?php echo $report['report_id']; ?>">
                                        <img src="<?php echo $report['product_img']; ?>" class="img-thumbnail">
                                    </a>
                                </td>
                                <td><?php echo $report['product_name']; ?></td>
                                <td><?php echo $report['quantity']; ?></td>
                                <td><?php echo $report['borrowed_from'] . ' - ' . $report['borrowed_till']; ?></td> <!-- Combine borrowed_from and borrowed_till -->
                                <td><?php echo $report['cause']; ?></td>
                                <td><?php echo $report['notes']; ?></td>
                                <td>
                                    <?php
                                    $status_color = '';
                                    switch ($report['report_status']) {
                                        case 'Under Review':
                                            $status_color = 'status-orange';
                                            break;
                                        case 'Confirmed':
                                            $status_color = 'status-green';
                                            break;
                                        case 'Cancelled':
                                            $status_color = 'status-red';
                                            break;
                                        default:
                                            $status_color = '';
                                            break;
                                    }
                                    ?>
                                    <span class="<?php echo $status_color; ?>"><?php echo $report['report_status']; ?></span>
                                </td>
                                <td>
                                <a href="edit_student_damage_report.php?report_id=<?php echo $report['report_id']; ?>" class="btn" style="background-color: transparent;"><span class="glyphicon glyphicon-edit" style="color: #007bff;"></span></a> <!-- Edit button -->
    <a href="delete_student_damage_report.php?report_id=<?php echo $report['report_id']; ?>" class="btn" style="background-color: transparent;"><span class="glyphicon glyphicon-trash" style="color: #dc3545;"></span></a> <!-- Delete button -->
    <a href="print_student_report.php?report_id=<?php echo $report['report_id']; ?>" class="btn" style="background-color: transparent;"><span class="glyphicon glyphicon-print" style="color: #6c757d;"></span></a> <!-- Print button -->

</td>

                                
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>

        <!-- Other Damage Reports section -->
        <?php foreach ($other_reports_by_cause as $cause => $reports): ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><?php echo $cause; ?></strong>
                </div>
                <div class="panel-body">
                <div style="max-height: 300px; overflow: auto;">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>report_id</th>
                                <th>product_img</th>
                                <th>product_name</th>
                                <th>Qty</th>
                                <th>date_of_incident</th>
                                <th>Asset Num</th>
                                <th>notes</th>
                                <th>report_status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td><?php echo $report['report_id']; ?></td>
                                    <td>
                                        <a href="#" data-toggle="modal" data-target="#imageModal<?php echo $report['report_id']; ?>">
                                            <img src="<?php echo $report['product_img']; ?>" class="img-thumbnail">
                                        </a>
                                    </td>
                                    <td><?php echo $report['product_name']; ?></td>
                                    <td><?php echo $report['quantity']; ?></td>
                                    <td><?php echo $report['date_of_incident']; ?></td>
                                    <td><?php echo $report['asset_num']; ?></td>

                                    <td><?php echo $report['notes']; ?></td>
                                    <td>
                                        <?php
                                        $status_color = '';
                                        switch ($report['report_status']) {
                                            case 'Under Review':
                                                $status_color = 'status-orange';
                                                break;
                                            case 'Confirmed':
                                                $status_color = 'status-green';
                                                break;
                                            case 'Resolved':
                                                $status_color = 'status-blue';
                                                break;
                                            default:
                                                $status_color = '';
                                                break;
                                        }
                                        ?>
                                        <span class="<?php echo $status_color; ?>"><?php echo $report['report_status']; ?></span>
                                    </td>
                                    <td>
    <a href="edit_other_damage_report.php?report_id=<?php echo $report['report_id']; ?>" class="btn" style="background-color: transparent;"><span class="glyphicon glyphicon-edit" style="color: #007bff;"></span></a> <!-- Edit button -->
    <a href="delete_other_damage_report.php?report_id=<?php echo $report['report_id']; ?>" class="btn" style="background-color: transparent;"><span class="glyphicon glyphicon-trash" style="color: #dc3545;"></span></a> <!-- Delete button -->
    <a href="print_other_report.php?report_id=<?php echo $report['report_id']; ?>" class="btn" style="background-color: transparent;"><span class="glyphicon glyphicon-print" style="color: #6c757d;"></span></a> <!-- Print button -->

</td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>


<?php include_once('layouts/footer.php'); ?>

<script>
    // Handle form submission using JavaScript
    document.getElementById('reportTypeForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission
        var reportType = document.getElementById('report_type').value;
        if (reportType === 'student') {
            window.location.href = 'student_damage_report_form.php';
        } else if (reportType === 'other') {
            window.location.href = 'other_damage_report_form.php';
        }
    });
</script>
