<?php
require_once('includes/functions.php');
require_once('includes/load.php');
page_require_level(1); // Adjust the required user level as needed
include_once('layouts/header.php');

// Check if report ID is provided via GET request
if (!isset($_GET['report_id'])) {
    $session->msg("d", "Report ID not provided.");
    redirect('damage_reports.php'); // Adjust as needed
}

$report_id = (int)$_GET['report_id'];
$report = find_by_report_id('student_damage_reports', $report_id);

if (!$report) {
    $session->msg("d", "Report not found.");
    redirect('damage_reports.php'); // Adjust as needed
}

// Fetch all borrow details from the database
$borrow_details = find_all('all_borrowers');

// Fetch product names based on asset numbers
$products = find_by_sql("SELECT name, asset_num FROM products");
$product_names = array();
foreach ($products as $product) {
    $product_names[$product['asset_num']] = $product['name'];
}

?>
<style>
     .img-thumbnail {
        height: 150px;
        width: 150px;
        vertical-align: middle;
    }
    .custom-file-label::after {
    content: " ";
}


</style>
<div class="container">
    <div class="row">
    <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Edit Student Damage Report</span>
        </strong>
      </div>
      <div class="panel-body">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Edit Student Damage Report</h3>
                </div>
                <div class="card-body">
                    <!-- Edit report form -->
                    <form id="editReportForm" action="edit_process_student_damage_report.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="report_id" value="<?php echo $report['report_id']; ?>">
                        <div class="form-group">
                            <label for="product_name">Product Name:</label>
                            <input type="text" class="form-control" name="product_name" id="product_name" value="<?php echo remove_junk($report['product_name']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="asset_num">Asset Number:</label>
                            <input type="text" class="form-control" name="asset_num" id="asset_num" value="<?php echo remove_junk($report['asset_num']); ?>" readonly>
                        </div>
                        <!-- Display existing image if available -->
                        <?php if (!empty($report['product_img'])) : ?>
                       
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" class="form-control" name="quantity" id="quantity" value="<?php echo remove_junk($report['quantity']); ?>" required>
                        </div>
                       
                        <div class="form-group">
                            <label for="cause">Cause of Damage:</label>
                            <textarea class="form-control" name="cause" id="cause" rows="3" required><?php echo remove_junk($report['cause']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes:</label>
                            <textarea class="form-control" name="notes" id="notes" rows="3"><?php echo remove_junk($report['notes']); ?></textarea>
                        </div>
                        <div class="form-group">
    <label for="product_img">Product Image:</label>
    <?php if (!empty($report['product_img'])) : ?>
        <br>
        <img src="<?php echo remove_junk($report['product_img']); ?>" class="img img-thumbnail" alt="Product Image">
    <?php endif; ?>
    <input type="file" class="custom-file-input custom-file-label form-control-file" name="product_img" id="product_img">
    <label class="custom-file-label" for="product_img">Replace Image</label>
</div>




                        <div class="form-group">
                            <label for="student_id">Student ID:</label>
                            <input type="text" class="form-control" name="student_id" id="student_id" value="<?php echo remove_junk($report['student_id']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="report_status">Report Status:</label>
                            <select class="form-control" name="report_status" id="report_status" required>
                                <option value="Under Review" <?php echo ($report['report_status'] == 'Under Review') ? 'selected' : ''; ?>>Under Review</option>
                                <option value="Confirmed" <?php echo ($report['report_status'] == 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="Resolved" <?php echo ($report['report_status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" name="submit">Submit Report</button>
                        </div>
                    </form>
                    <!-- End of damage report form -->
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php include_once('layouts/footer.php'); ?>
