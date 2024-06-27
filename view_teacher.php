<?php
$page_title = 'Teacher Details';
require_once('includes/load.php');
require_once('includes/functions.php');

// Check the user's permission level
page_require_level(1);

// Get teacher details based on the provided teacher ID
if (isset($_GET['id'])) {
    $teacher_id = (int)$_GET['id'];
    $teacher = find_by_id('teachers', $teacher_id);
    if (!$teacher) {
        $session->msg("d", "Teacher not found!");
        redirect('borrowers.php');
    }
} else {
    $session->msg("d", "Missing teacher ID!");
    redirect('borrowers.php');
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-user"></span>
                    <span>Teacher Details</span>
                </strong>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                        <?php
            $identifier = $teacher['employee_id'];

            // Generate the SVG data using JsBarcode
            $svgData = "<svg id='barcode_" . $teacher['id'] . "' class='barcode-svg' width='50' height='25'></svg><script>JsBarcode('#barcode_" . $teacher['id'] . "', '" . $identifier . "');</script>";
        ?>
        <tr>
            <th>Barcode:</th>
            <td><?php echo $svgData; ?>
        </td>
                            <tr>
                                <td>Name:</td>
                                <td><?php echo $teacher['name']; ?></td>
                            </tr>
                            <tr>
                                <td>Email:</td>
                                <td><?php echo $teacher['email']; ?></td>
                            </tr>
                            <tr>
                                <td>Department:</td>
                                <td><?php echo $teacher['department']; ?></td>
                            </tr>
                            <tr>
                                <td>Employee ID:</td>
                                <td>
                                <?php echo $teacher['employee_id']; ?>
                                </td>
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
    // Generate barcode for employee_id
    var employeeId = <?php echo $teacher['employee_id']; ?>;
    JsBarcode("#barcode", employeeId, { format: "CODE128" });
</script>
