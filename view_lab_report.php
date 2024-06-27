<?php
require_once('includes/load.php');
page_require_level(1); // Requires admin level for accessing this page

// Check if report_id is provided in the URL
if (isset($_GET['report_id'])) {
    $report_id = $_GET['report_id'];

    // Fetch the damage report from the database based on the report_id
    $damage_report = find_report_by_report_id('lab_damage_reports', $report_id);

    // Check if the damage report exists
    if ($damage_report) {
        // Display the damage report details
        include_once('layouts/header.php');
?>
        <style>
            /* CSS for setting max width of image */
            .damage-image {
                max-width: 50%; /* Adjust the max width as needed */
                height: auto; /* Maintain aspect ratio */
            }
        </style>
      
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <strong>
                                                <span class="glyphicon glyphicon-user"></span>
                                                <span>Damage Report Details</span>
                                            </strong>
                                        </div>
                                        
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tbody>
                                                    <ul class="list-group">
                                <li class="list-group-item"><img src="uploads/<?php echo $damage_report['picture']; ?>" class="img-fluid damage-image" alt="Damage Picture"></li>
                                <li class="list-group-item"><strong>Report ID:</strong> <?php echo $damage_report['report_id']; ?></li>
                               
                                <li class="list-group-item"><strong>Cause:</strong> <?php echo $damage_report['cause']; ?></li>
                                <li class="list-group-item"><strong>Notes:</strong> <?php echo $damage_report['notes']; ?></li>
                                <li class="list-group-item"><strong>Report Status:</strong> <?php echo $damage_report['report_status']; ?></li>
                                <li class="list-group-item"><strong>Report Created:</strong> <?php echo $damage_report['report_created']; ?></li>
                            </ul>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                      
<?php
        include_once('layouts/footer.php');
    } else {
        // If the damage report does not exist
        $session->msg('d', 'Error: Damage report not found.');
        redirect('all_other_reports.php'); // Redirect to damage_report.php or any other appropriate page
    }
} else {
    // If report_id is not provided in the URL
    $session->msg('d', 'Error: Report ID is missing.');
    redirect('all_other_reports.php'); // Redirect to damage_report.php or any other appropriate page
}
?>
