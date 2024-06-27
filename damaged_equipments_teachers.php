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
$reserved_equipment = find_all('teacher_reserved');

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

        <div class="col-md-6 mx-auto">
    <div class="white-box p-4">
        <form method="post" action="report_submit_teachers.php" class="form-horizontal" enctype="multipart/form-data">
            <div class="form-group">
            <h1 class="text-center">Teacher Damaged Equipment Report<hr style="border-color: #FFFFFF;">
</h1>
<!-- Inside the form -->

    <form method="post" action="report_submit_teachers.php" class="form-horizontal" enctype="multipart/form-data">
    <div class="form-group">
    <label for="reserved_equipment" class="control-label">Reserved Equipment:</label>
    <select name="reserved_equipment" id="reserved_equipment" class="form-control" required>
        <option value="">Select Reserved Equipment</option>
        <?php foreach ($reserved_equipment as $row): ?>
            <?php
            // Format the reservation dates
            $reservation_date_from = date('M d, Y, g:i a', strtotime($row['reservation_date_from']));
            $reservation_date_till = date('M d, Y, g:i a', strtotime($row['reservation_date_till']));
            ?>
            <option value="<?php echo $row['id']; ?>" 
                    data-employee_id="<?php echo $row['employee_id']; ?>" 
                    data-teacher-name="<?php echo $row['teacher_name']; ?>" 
                    data-department="<?php echo $row['department']; ?>">
                <?php echo "{$row['teacher_name']} - {$row['equipment']} - Qty: {$row['quantity']} - Reserved from {$reservation_date_from} to {$reservation_date_till}"; ?>
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
            <label for="employee_id" class="control-label">Teacher ID:</label>
            <input type="text" name="employee_id" id="employee_id" class="form-control" readonly required>
        </div>
        <div class="form-group">
            <label for="teacher_name" class="control-label">Teacher Name:</label>
            <input type="text" name="teacher_name" id="teacher_name" class="form-control" readonly required>
        </div>
        <div class="form-group">
            <label for="department" class="control-label">Department:</label>
            <input type="text" name="department" id="department" class="form-control" readonly required>
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
        <div class="form-group">
            <button type="submit" name="submit" class="btn btn-primary">Submit Report</button>
        </div>
    </form>
</div>
</div>
</div>

<script>
    document.getElementById('reserved_equipment').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        document.getElementById('employee_id').value = selectedOption.getAttribute('data-employee_id');
        document.getElementById('teacher_name').value = selectedOption.getAttribute('data-teacher-name');
        document.getElementById('department').value = selectedOption.getAttribute('data-department');
    });
</script>

<?php include_once('layouts/footer.php'); ?>
