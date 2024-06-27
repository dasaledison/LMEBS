<?php
// Include necessary files and start session if required
require_once('includes/load.php');
// Check user's permission level
page_require_level(1);

// Check if school ID is provided
if (isset($_GET['school_id'])) {
    // Retrieve scanned school ID
    $scanned_school_id = $_GET['school_id'];
    
    // Function to get borrower ID from scanned school ID with status = In Use
    function get_borrower_id($school_id) {
        global $db;
        $sql = "SELECT borrower_id FROM all_borrowers WHERE school_id = '$school_id' AND status = 'In Use'";
        $result = $db->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['borrower_id'];
        } else {
            return null;
        }
    }

    // Function to get equipment details based on borrower ID
    function get_equipment_details($borrower_id) {
        global $db;
        $equipment_details = array();
        $sql = "SELECT asset_num, quantity FROM all_borrowers WHERE borrower_id = '$borrower_id'";
        $result = $db->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Fetch name from products table based on asset_num
                $asset_num = $row['asset_num'];
                $name_sql = "SELECT name FROM products WHERE asset_num = '$asset_num'";
                $name_result = $db->query($name_sql);
                if ($name_result->num_rows > 0) {
                    $name_row = $name_result->fetch_assoc();
                    $row['name'] = $name_row['name'];
                }
                $equipment_details[] = $row;
            }
        }
        return $equipment_details;
    }

    // Fetch borrower ID
    $borrower_id = get_borrower_id($scanned_school_id);

    if ($borrower_id) {
        // Fetch equipment details
        $equipment_details = get_equipment_details($borrower_id);

        // Include the header
        include_once('layouts/header.php');
        ?>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

        <!-- Borrow Details Panel -->
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong>
                            <span class="glyphicon glyphicon-th"></span>
                            <span>Return Form</span>
                        </strong>
                    </div>
                    <div class="panel-body">
                        <form action="return_equipment_action.php" method="post">

                            <!-- Borrower ID and Equipment Details -->
                            <p><strong>Borrower ID:</strong> <?php echo $borrower_id; ?></p>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Equipment</th>
                                        <th>Asset Number</th>
                                        <th>Quantity</th>
                                        <th>Good Condition</th>
                                        <th>Bad Condition</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($equipment_details as $item): ?>
                                        <tr>
                                            <td><?php echo $item['name']; ?></td>
                                            <td><?php echo $item['asset_num']; ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>
                                                <input type="number" class="form-control" id="good_condition_<?php echo $item['asset_num']; ?>" name="good_condition_<?php echo $item['asset_num']; ?>" min="0" value="0" >
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" id="bad_condition_<?php echo $item['asset_num']; ?>" name="bad_condition_<?php echo $item['asset_num']; ?>" min="0" value="0" >
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary" onclick="return validateQuantities()">Submit</button>
                            <input type="hidden" name="borrower_id" value="<?php echo $borrower_id; ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>

       <!-- Script for validating inputted quantities -->
    <!-- Script for validating inputted quantities -->
<script>
    function validateQuantities() {
        var isValid = true;
        <?php foreach ($equipment_details as $item): ?>
            var goodCondition = parseInt(document.getElementById("good_condition_<?php echo $item['asset_num']; ?>").value);
            var badCondition = parseInt(document.getElementById("bad_condition_<?php echo $item['asset_num']; ?>").value);
            var totalQuantity = <?php echo $item['quantity']; ?>;
            
            if (isNaN(goodCondition) || isNaN(badCondition) || goodCondition < 0 || badCondition < 0) {
                alert("Invalid quantities for <?php echo $item['name']; ?>. Good condition and bad condition must be positive numbers.");
                isValid = false;
                break;
            }
            
            var totalConditions = goodCondition + badCondition;
            if (totalConditions !== totalQuantity) {
                alert("Total quantity of good and bad conditions must match the available quantity for <?php echo $item['name']; ?>.");
                isValid = false;
                break;
            }
        <?php endforeach; ?>
        return isValid;
    }
</script>







        <?php
        // Include the footer
        include_once('layouts/footer.php');
    } else {
        // If no borrower ID found for the scanned school ID
        $session->msg('d', 'The scanned school ID has not borrowed any equipment yet. Please borrow an equipment first.');


        redirect('return_equipment.php'); // Redirect to borrow_equipment.php or any other appropriate page
    }
} else {
    // If school ID is not provided
    $session->msg('d', 'School ID is missing.');
    redirect('return_equipment.php'); // Redirect to borrow_equipment.php or any other appropriate page
}
?>
