<?php
$page_title = 'Edit Teacher';
require_once('includes/load.php');

// Check the user's permission level to view this page
page_require_level(2);

// Get the teacher ID from the URL parameter
$teacher_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Find the teacher data by ID
$teacher = find_by_id('teachers', $teacher_id);

// Function to check if the employee ID already exists in the database, excluding the current teacher being edited
function employee_id_exists_exclude_current($employee_id, $current_teacher_id) {
    global $db;

    // Escape inputs to prevent SQL injection
    $employee_id = $db->escape($employee_id);
    $current_teacher_id = $db->escape($current_teacher_id);

    // Query to check if the employee ID exists excluding the current teacher
    $query = "SELECT COUNT(*) AS total FROM teachers WHERE employee_id = '{$employee_id}' AND id != '{$current_teacher_id}'";
    $result = $db->query($query);

    // Check if any rows were found with the given employee ID
    $row = $result->fetch_assoc();
    return ($row['total'] > 0);
}

// Check if the teacher exists
if (!$teacher) {
    $session->msg('d', 'Teacher not found!');
    redirect('borrowers.php');
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $req_fields = array('teacher_name', 'teacher_email', 'teacher_employee_id');
    validate_fields($req_fields);

    if (empty($errors)) {
        $name = $db->escape($_POST['teacher_name']);
        $email = $db->escape($_POST['teacher_email']);
        $employee_id = $db->escape($_POST['teacher_employee_id']);

        // Check if the employee ID already exists in the database, excluding the current teacher being edited
        if (employee_id_exists_exclude_current($employee_id, $teacher_id)) {
            $session->msg('d', 'Employee ID already exists.');
            redirect('borrowers.php',false);
        }

        // Update teacher data in the database
        $sql = "UPDATE teachers SET ";
        $sql .= "name='{$name}', email='{$email}', ";
        $sql .= "employee_id='{$employee_id}' ";
        $sql .= "WHERE id='{$teacher_id}'";
        $result = $db->query($sql);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Teacher updated successfully!');
            redirect('borrowers.php', false);
        } else {
            $session->msg('d', 'Failed to update teacher!');
            redirect('edit_teacher.php?id=' . $teacher_id, false);
        }
    } else {
        // Errors found, display error message and stay on the edit page
        $session->msg("d", $errors);
        redirect('edit_teacher.php?id=' . $teacher_id, false);
    }
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Edit Teacher</span>
                </strong>
            </div>
            <div class="panel-body">
                <form action="edit_teacher.php?id=<?php echo $teacher_id; ?>" method="POST">
                    <div class="form-group">
                        <label for="teacher_name">Teacher Name:</label>
                        <input type="text" class="form-control" id="teacher_name" name="teacher_name" value="<?php echo remove_junk(ucwords($teacher['name'])); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="teacher_email">Teacher Email:</label>
                        <input type="email" class="form-control" id="teacher_email" name="teacher_email" value="<?php echo remove_junk($teacher['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="teacher_employee_id">Teacher Employee ID:</label>
                        <input type="text" class="form-control" id="teacher_employee_id" name="teacher_employee_id" value="<?php echo remove_junk($teacher['employee_id']); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Teacher</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
