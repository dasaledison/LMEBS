<?php
$page_title = 'Edit Student';
require_once('includes/load.php');

// Check the user's permission level to view this page
page_require_level(2);

// Get the student ID from the URL parameter
$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Find the student data by ID
$student = find_by_id('students', $student_id);
// Define a function to check if a student ID already exists in the database excluding the current student being edited
function student_id_exists_exclude_current($student_id, $current_student_id) {
    global $db; // Assuming $db is your database connection object

    // Escape inputs to prevent SQL injection
    $student_id = $db->escape($student_id);
    $current_student_id = $db->escape($current_student_id);

    // Query to check if the student ID exists excluding the current student
    $query = "SELECT COUNT(*) AS total FROM students WHERE student_id = '{$student_id}' AND id != '{$current_student_id}'";
    $result = $db->query($query);

    // Check if any rows were found with the given student ID
    $row = $result->fetch_assoc();
    return ($row['total'] > 0);
}

// Check if the student exists
if (!$student) {
    $session->msg('s', 'Student updated successfully!');
    redirect('borrowers.php', false);
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $req_fields = array('name', 'email', 'department', 'course', 'section', 'student_id');
    validate_fields($req_fields);

    if (empty($errors)) {
        $name = $db->escape($_POST['name']);
        $email = $db->escape($_POST['email']);
        $department = $db->escape($_POST['department']);
        $course = $db->escape($_POST['course']);
        $section = $db->escape($_POST['section']);
        $student_id = $db->escape($_POST['student_id']);

        // Check if the student ID already exists excluding the current student being edited
        if (student_id_exists_exclude_current($student_id, $student['id'])) {
            $session->msg('d', 'Student ID already exists.');
            redirect('borrowers.php',false);
        }

        // Update student data in the database
        $sql = "UPDATE students SET ";
        $sql .= "name='{$name}', email='{$email}', ";
        $sql .= "department='{$department}', course='{$course}', ";
        $sql .= "section='{$section}', student_id='{$student_id}' ";
        $sql .= "WHERE id='{$student['id']}'"; // Update based on the student's ID
        
        $result = $db->query($sql);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Student updated successfully!');
            redirect('borrowers.php', false);
        } else {
            $session->msg('d', 'Failed to update student!');
            redirect('edit_student.php?id=' . $student_id, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_student.php?id=' . $student_id, false);
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
                    <span>Edit Student</span>
                </strong>
            </div>
            <div class="panel-body">
                <form action="edit_student.php?id=<?php echo $student_id; ?>" method="POST">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($student['name']) ? remove_junk(ucwords($student['name'])) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($student['email']) ? remove_junk($student['email']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="department">Department:</label>
                        <input type="text" class="form-control" id="department" name="department" value="<?php echo isset($student['department']) ? remove_junk(ucwords($student['department'])) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="course">Course:</label>
                        <input type="text" class="form-control" id="course" name="course" value="<?php echo isset($student['course']) ? remove_junk(ucwords($student['course'])) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="section">Section:</label>
                        <input type="text" class="form-control" id="section" name="section" value="<?php echo isset($student['section']) ? remove_junk(ucwords($student['section'])) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="student_id">Student ID:</label>
                        <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo isset($student['student_id']) ? remove_junk($student['student_id']) : ''; ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Student</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
