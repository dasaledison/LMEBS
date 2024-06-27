<?php
// Page title and required files
$page_title = 'Input Borrow Details';
require_once('includes/load.php');
// Check what level user has permission to view this page
page_require_level(1);

// Retrieve student ID, equipment barcodes, and quantities from the URL
$student_id = isset($_GET['student-id']) ? $_GET['student-id'] : '';
$equipment_barcodes = isset($_GET['equipment_barcode']) ? $_GET['equipment_barcode'] : array();
$quantities = isset($_GET['quantity']) ? $_GET['quantity'] : array();
$student_subjects = find_subjects_by_student_id($student_id);

// If equipment barcodes or quantities are strings, convert them to arrays
if (!is_array($equipment_barcodes)) {
    $equipment_barcodes = array($equipment_barcodes);
}
if (!is_array($quantities)) {
    $quantities = array($quantities);
}

// Fetch student details based on the provided student ID
$student_details = find_student_by_id($student_id);
function find_by_barcode($barcode) {
    global $db;
    $barcode = $db->escape($barcode);
    $sql = "SELECT * FROM products WHERE equipment_barcode = '{$barcode}' LIMIT 1";
    $result = $db->query($sql);
    if ($db->num_rows($result) > 0) {
        return $db->fetch_assoc($result);
    } else {
        return null;
    }
}
// Include header
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
    
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Borrow Details</span>
                </strong>
            </div>
            <div class="panel-body">
                <!-- Display student and product details -->
                <h4><?php echo isset($student_details['name']) ? $student_details['name'] : ''; ?></h4>
                <p><?php echo isset($student_details['student_id']) ? $student_details['student_id'] : ''; ?></p>
                <p><?php echo isset($student_details['email']) ? $student_details['email'] : ''; ?></p>
                <!-- Display class subject times -->
                <br>
                <?php
                // Assuming you have a function to fetch student subjects
                $student_subjects = find_subjects_by_student_id($student_id);

                // Check if subjects are found before looping
                if (!empty($student_subjects)) {
                    foreach ($student_subjects as $subject) :
                ?>
                <?php
                    endforeach;
                } else {
                    echo "<p>No subjects found for this student.</p>";
                }
                ?>
                <form method="post" action="process_borrow.php" onsubmit="return validateQuantity()">
                <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">

                  <?php if (!empty($student_subjects)) : ?>
                        <label for="subject_id">Select Subject:</label>
                        <select class="form-control" name="subject_id" id="subject_id" required>
                            <?php foreach ($student_subjects as $subject) : ?>
                                <option value="<?php echo $subject['id']; ?>"><?php echo $subject['name']; ?> <?php echo isset($subject['from']) ? '(' . $subject['from'] . ' - ' . $subject['till'] . ')' : ''; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <!-- Gray hyperlink for "Change Time" -->
                        <a href="#" id="changeTimeLink" style="color: gray;">Change Time</a><br><br>
                        <!-- Custom time entry fields -->
                        <div id="customTimeFields" style="display: none;">
                            <div class="form-group">
                                <label for="customStartTime">Custom Start Time:</label>
                                <input class="form-control" type="time" name="custom_start_time" id="customStartTime" placeholder="Enter start time">
                            </div>
                            <div class="form-group">
                                <label for="customEndTime">Custom End Time:</label>
                                <input class="form-control" type="time" name="custom_end_time" id="customEndTime" placeholder="Enter end time">
                            </div>
                        </div>
                    <?php endif; ?>
                <br>
              
                <h4>Borrowing for:</h4>

                    <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                        <th>Equipment Name</th>
                            <th>Asset Number</th>
                            <th>Quantity</th>
                      
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Check if equipment barcode and quantity are scanned and passed in the URL
                        if (isset($_GET['equipment_barcode'])) {
                            // Loop through each equipment barcode and quantity in the URL
                            $scanned_items = array();
                            parse_str($_SERVER['QUERY_STRING'], $query_params);
                            foreach ($query_params['equipment_barcode'] as $key => $barcode) {
                                $quantity = $query_params['quantity'][$key];
                                // Retrieve product information
                                $product = find_by_barcode($barcode);
                                if ($product) {
                                    // Display scanned item
                                    echo "<tr>";
                                    echo "<td>{$product['name']}</td>";
                                    echo "<td>{$product['asset_num']}</td>";
                                    echo "<td>{$quantity}</td>";
                                   
                                    echo "</tr>";
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <?php foreach ($query_params['equipment_barcode'] as $key => $barcode) : ?>
    <input type="hidden" name="equipment_barcode[]" value="<?php echo $barcode; ?>">
<?php endforeach; ?>
<?php foreach ($query_params['quantity'] as $key => $quantity) : ?>
    <input type="hidden" name="quantity[]" value="<?php echo $quantity; ?>">
<?php endforeach; ?>
                    <button type="submit" class="btn btn-success">Save and Borrow</button>
                </form>
                <br>
                <!-- Button to submit another one -->
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
<script>
    document.getElementById('changeTimeLink').addEventListener('click', function(event) {
        event.preventDefault();
        var customTimeFields = document.getElementById('customTimeFields');
        if (customTimeFields.style.display === 'none') {
            customTimeFields.style.display = 'block';
        } else {
            customTimeFields.style.display = 'none';
        }
    });

    function validateQuantity() {
        var quantity = parseInt(document.getElementById('quantity').value);
        var availableQuantity = parseInt("<?php echo isset($product_details['quantity']) ? $product_details['quantity'] : '0'; ?>");
        if (quantity > availableQuantity) {
            document.getElementById('quantityError').style.display = 'block';
            return false;
        } else if (quantity <= 0) {
            document.getElementById('quantityError').style.display = 'block';
            return false;
        }
        return true;
    }
</script>