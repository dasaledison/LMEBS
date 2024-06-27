<?php
require_once('includes/functions.php');
require_once('includes/load.php');
page_require_level(1); // Adjust the required user level as needed
include_once('layouts/header.php');

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
    /* Add the following styles to your existing CSS */
    .custom-file-input {
        cursor: pointer;
    }

    .custom-file-input::-webkit-file-upload-button {
        visibility: hidden;
    }

    .custom-file-input::before {
        content: 'Choose File';
        display: inline-block;
        background: #007bff;
        color: #fff;
        border: 1px solid #007bff;
        border-radius: 5px;
        padding: 5px 10px;
        outline: none;
        white-space: nowrap;
        cursor: pointer;
    }

    .custom-file-input:hover::before {
        border-color: #0056b3;
    }

    .custom-file-input:active::before {
        background: #0056b3;
    }

    .custom-file-input::after {
        content: 'No file chosen';
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Student Damage Report Form</h3>
                </div>
                <div class="card-body">
                    <!-- Damage report form -->
                    <form id="damageReportForm" action="process_student_damage_report.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="borrow_id">Borrow Details:</label>
                            <select class="form-control" name="borrow_id" id="borrow_id" required>
                                <option value="">Select Borrow Details</option>
                                <?php foreach ($borrow_details as $borrow) : ?>
                                    <?php $borrow_info = "{$borrow['borrow_id']} - School ID: {$borrow['school_id']}, {$borrow['borrowed_from']}, {$borrow['borrowed_till']}, Quantity: {$borrow['quantity']}, Asset Number: {$borrow['asset_num']}, Status: {$borrow['status']}"; ?>
                                    <option value="<?php echo $borrow['borrow_id']; ?>"><?php echo $borrow_info; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="product_name">Equipment Name:</label>
                            <input type="text" class="form-control" name="product_name" id="product_name" readonly>
                        </div>
                        <div class="form-group">
                            <label for="asset_num">Asset Number:</label>
                            <input type="text" class="form-control" name="asset_num" id="asset_num" readonly>
                        </div>
                        <div class="form-group">
                            <label for="student_id">Student ID:</label>
                            <input type="text" class="form-control" name="student_id" id="student_id" readonly>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" class="form-control" name="quantity" required>
                        </div>
                   
                        <div class="form-group">
                            <label for="cause">Cause of Damage:</label>
                            <textarea class="form-control" name="cause" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes:</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="product_img">Product Image:</label>
                            <input type="file" class="custom-file-input form-control-file" name="product_img" id="product_img">
                            <span id="file-name" class="placeholder">No file chosen</span>
                        </div>
                     
                        <div class="form-group">
                            <label for="report_status">Report Status:</label>
                            <select class="form-control" name="report_status" required>
                                <option value="Under Review">Under Review</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Resolved">Resolved</option>
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
<script>
    // Update product name field, asset number field, and student id when the borrow details are selected
    document.getElementById('borrow_id').addEventListener('change', function() {
        console.log('Borrow ID changed'); // Debug statement

        var selectedBorrowId = this.value;
        console.log('Selected Borrow ID:', selectedBorrowId); // Debug statement

        var productNameField = document.getElementById('product_name');
        var assetNumField = document.getElementById('asset_num');
        var studentIdField = document.getElementById('student_id');

        // Get the selected borrow details
        var selectedBorrowDetails = <?php echo json_encode($borrow_details); ?>.find(borrow => borrow.borrow_id === selectedBorrowId);
        console.log('Selected Borrow Details:', selectedBorrowDetails); // Debug statement

        // Update fields if borrow details are found
        if (selectedBorrowDetails) {
            assetNumField.value = selectedBorrowDetails.asset_num || '';

            // Fetch product name based on asset number
            var productName = <?php echo json_encode($product_names); ?>[selectedBorrowDetails.asset_num];
            productNameField.value = productName || '';

            // Set student id
            var studentId = selectedBorrowDetails.school_id; // Assuming school_id is the student_id
            studentIdField.value = studentId || '';
        } else {
            // Clear fields if no borrow details are found
            productNameField.value = '';
            assetNumField.value = '';
            studentIdField.value = '';
        }
    });
</script>

<?php include_once('layouts/footer.php'); ?>
