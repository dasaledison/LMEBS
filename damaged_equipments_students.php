<?php
// Include necessary files
require_once('includes/config.php');
require_once('includes/load.php');

// Check if the user is logged in
if (!$session->isUserLoggedIn(true)) {
    redirect('index.php', false);
}

// Set the page title
$page_title = 'Damaged Equipment Report';
include_once('layouts/header.php');

// Fetch reserved equipment data from the database
$reserved_equipment = find_all('student_reserved');

// Handle form submission in report_submit.php
?>
<style>
    .white-box {
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 70px;
        margin-top: 20px;
    }

    .white-box h1, .white-box h2, .white-box h3, .white-box h4, .white-box h5, .white-box h6 {
      
        margin: 10px;
    }

    .white-box form {
        margin-bottom: 0;
    }

    .white-box .form-group {
        margin-bottom: 20px;
    }

    .white-box .form-control {
        box-shadow: none;
    }

    .white-box .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .white-box .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    

<!-- Inside the form -->
<div class="col-md-6 mx-auto">
    <div class="white-box p-4">
        <form method="post" action="report_submit.php" class="form-horizontal" enctype="multipart/form-data">
            <div class="form-group">
            <h1 class="text-center">Student Damaged Equipment Report<hr style="border-color: #FFFFFF;">
</h1>

            <label for="reserved_equipment" class="control-label">Select Reserved Equipment:</label>
<select name="reserved_equipment" id="reserved_equipment" class="form-control" required>
    <option value="">Choose Reserved Equipment</option>
    <?php foreach ($reserved_equipment as $row): ?>
        <?php
        // Format the reservation dates
        $reservation_date_from = date('M d, Y, g:i a', strtotime($row['reservation_date_from']));
        $reservation_date_till = date('M d, Y, g:i a', strtotime($row['reservation_date_till']));
        ?>
        <option value="<?php echo $row['id']; ?>" 
                data-student-id="<?php echo $row['student_id']; ?>" 
                data-student-name="<?php echo $row['student_name']; ?>" 
                data-section="<?php echo $row['section']; ?>">
            <?php echo "{$row['student_name']} - {$row['equipment']} (Qty: {$row['quantity']}, Reserved from {$reservation_date_from} to {$reservation_date_till})"; ?>
        </option>
    <?php endforeach; ?>
</select>

            </div>
            <div class="form-group">
                <label for="quantity" class="control-label">Quantity:</label>
                <input type="number" name="quantity" id="quantity" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="date_of_incident" class="control-label">Date and Time of Incident:</label>
                <input type="datetime-local" name="date_of_incident" id="date_of_incident" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="cause" class="control-label">Cause:</label>
                <select name="cause" id="cause" class="form-control" required>
                    <option value="Missing">Missing</option>
                    <option value="Broken">Broken</option>
                </select>
            </div>
            <div class="form-group">
                <label for="student_id" class="control-label">Student ID:</label>
                <input type="text" name="student_id" id="student_id" class="form-control" readonly required>
            </div>
            <div class="form-group">
                <label for="student_name" class="control-label">Student Name:</label>
                <input type="text" name="student_name" id="student_name" class="form-control" readonly required>
            </div>
            <div class="form-group">
                <label for="section" class="control-label">Section:</label>
                <input type="text" name="section" id="section" class="form-control" readonly required>
            </div>
            <div class="form-group">
                <label for="notes" class="control-label">Notes:</label>
                <textarea name="notes" id="notes" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="fileToUpload" class="control-label">Upload Picture of Damaged Item:</label>
                <input type="file" name="fileToUpload" id="fileToUpload" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="report_status" class="control-label">Report Status:</label>
                <select name="report_status" id="report_status" class="form-control" required>
                    <option value="Under Review">Under Review</option>
                    <option value="Confirmed">Confirmed</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="form-group text-center mt-4">
                <button type="submit" name="submit" class="btn btn-primary">Submit Report</button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
<!-- Add this script along with the previous one -->
<!-- Replace the existing script with this one -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the form and relevant elements
        var form = document.querySelector('form');
        var reservedEquipmentSelect = document.getElementById('reserved_equipment');
        var quantityInput = document.getElementById('quantity');

        // Additional script for updating student details on select change
        reservedEquipmentSelect.addEventListener('change', function () {
            var selectedOption = this.options[this.selectedIndex];
            document.getElementById('student_id').value = selectedOption.getAttribute('data-student-id');
            document.getElementById('student_name').value = selectedOption.getAttribute('data-student-name');
            document.getElementById('section').value = selectedOption.getAttribute('data-section');

            // Update the max attribute of quantity input
            quantityInput.max = selectedOption.dataset.quantity;
        });

        // Event listener for form submission
        form.addEventListener('submit', function (event) {
            // Get the selected option
            var selectedOption = reservedEquipmentSelect.options[reservedEquipmentSelect.selectedIndex];

            // Get the quantity input value
            var quantityValue = parseInt(quantityInput.value);

            // Check if quantity exceeds the reserved quantity
            if (isNaN(quantityValue) || quantityValue <= 0 || quantityValue > parseInt(selectedOption.dataset.quantity)) {
                // Prevent form submission
                event.preventDefault();
                // Display an alert or any other user feedback as needed
                alert('Please enter a valid quantity within the reserved quantity limit.');
            }
        });
    });
</script>


<?php include_once('layouts/footer.php'); ?>
