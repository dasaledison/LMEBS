<?php
// Page title and required files
$page_title = 'Input Borrow Details';
require_once('includes/load.php');
// Check what level user has permission to view this page
page_require_level(1);

// Retrieve employee ID, equipment barcodes, and quantities from the URL
$employee_id = isset($_GET['employee-id']) ? $_GET['employee-id'] : '';
$equipment_barcodes = isset($_GET['equipment_barcode']) ? $_GET['equipment_barcode'] : array();
$quantities = isset($_GET['quantity']) ? $_GET['quantity'] : array();

// If equipment barcodes or quantities are strings, convert them to arrays
if (!is_array($equipment_barcodes)) {
    $equipment_barcodes = array($equipment_barcodes);
}
if (!is_array($quantities)) {
    $quantities = array($quantities);
}

// Fetch teacher details based on the provided employee ID
$teacher_details = find_teacher_by_employee_id($employee_id);

function find_subjects_by_employee_id($teacher_id) {
    global $db;
    $teacher_id = $db->escape($teacher_id);
    $sql = "SELECT * FROM subjects WHERE teacher_employee_id = '{$teacher_id}'";
    return find_by_sql($sql);
}



// Function to retrieve product details by barcode
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
                <!-- Display teacher details -->
                <h4><?php echo isset($teacher_details['name']) ? $teacher_details['name'] : ''; ?></h4>
                <p><?php echo isset($teacher_details['email']) ? $teacher_details['email'] : ''; ?></p>
                <p><?php echo isset($teacher_details['department']) ? $teacher_details['department'] : ''; ?></p>
                <!-- Display subjects and their assigned times -->
                <br>
                <?php
                // Assuming you have a function to fetch student subjects
                $teacher_subjects = find_subjects_by_employee_id($employee_id);

                // Check if subjects are found before looping
                if (!empty($teacher_subjects)) {
                    foreach ($teacher_subjects as $subject) :
                ?>
                <?php
                    endforeach;
                } else {
                    echo "<p>No subjects found for this student.</p>";
                }
                ?>
                <form method="post" action="process_borrow.php" onsubmit="return validateQuantity()">
                <input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>">

                  <?php if (!empty($teacher_subjects)) : ?>
                        <label for="subject_id">Select Subject:</label>
                        <select class="form-control" name="subject_id" id="subject_id" required>
                            <?php foreach ($teacher_subjects as $subject) : ?>
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
                
                    <br>
                    <h4>Borrowing for:</h4>
                    <!-- Display scanned items -->
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
                            // Loop through each equipment barcode and quantity in the URL
                            foreach ($equipment_barcodes as $key => $barcode) {
                                $quantity = $quantities[$key];
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
                            ?>
                        </tbody>
                    </table>
                    <!-- Hidden fields to pass scanned items -->
                    <?php foreach ($equipment_barcodes as $key => $barcode) : ?>
                        <input type="hidden" name="equipment_barcode[]" value="<?php echo $barcode; ?>">
                    <?php endforeach; ?>
                    <?php foreach ($quantities as $key => $quantity) : ?>
                        <input type="hidden" name="quantity[]" value="<?php echo $quantity; ?>">
                    <?php endforeach; ?>
                    <!-- Button to submit the form -->
                    <button type="submit" class="btn btn-success">Save and Borrow</button>
                </form>
                <br>
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