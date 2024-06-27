<?php
$page_title = 'Student Details';
require_once('includes/load.php');
require_once('includes/functions.php');

// Check the user's permission level
page_require_level(1);

// Get student details based on the provided student ID
if (isset($_GET['id'])) {
    $student_id = (int)$_GET['id'];
    $student = find_by_id('students', $student_id);
    if (!$student) {
        $session->msg("d", "Student not found!");
        redirect('students.php');
    }
} else {
    $session->msg("d", "Missing student ID!");
    redirect('students.php');
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-user"></span>
                    <span>Student Details</span>
                </strong>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                            <?php
            $identifier = $student['student_id'];

            // Generate the SVG data using JsBarcode
            $svgData = "<svg id='barcode_" . $student['id'] . "' class='barcode-svg' width='50' height='25'></svg><script>JsBarcode('#barcode_" . $student['id'] . "', '" . $identifier . "');</script>";
        ?>
        <tr>
            <th>Barcode:</th>
            <td><?php echo $svgData; ?>
        </td>
</tr>
                                <th>Name:</th>
                                <td><?php echo $student['name']; ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?php echo $student['email']; ?></td>
                            </tr>
                            <tr>
                                <th>Department:</th>
                                <td><?php echo $student['department']; ?></td>
                            </tr>
                            <tr>
                                <th>Course:</th>
                                <td><?php echo $student['course']; ?></td>
                            </tr>
                            <tr>
                                <th>Section:</th>
                                <td><?php echo $student['section']; ?></td>
                            </tr>
                        
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel-footer">
                <a href="borrowers.php" class="btn btn-default">Back</a>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3/dist/JsBarcode.all.min.js"></script>
<script>
    // Generate barcode for student_id
    var studentId = <?php echo $student['student_id']; ?>;
    JsBarcode("#barcode", studentId, { format: "CODE128" });
</script>
