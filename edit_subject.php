<?php
$page_title = 'Edit Subject';
require_once('includes/load.php');

// Check the user's permission level to view this page
page_require_level(2);

// Get the subject ID from the URL parameter
$subject_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Find the subject data by ID
$subject = find_by_id('subjects', $subject_id);

// Check if the subject exists
if (!$subject) {
    $session->msg('d', 'Subject not found!');
    redirect('subjects.php');
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $req_fields = array('subject_name', 'semester', 'from', 'till', 'teacher_employee_id');
    validate_fields($req_fields);

    if (empty($errors)) {
        $name = $db->escape($_POST['subject_name']);
        $semester = $db->escape($_POST['semester']);
        $from = $db->escape($_POST['from']);
        $till = $db->escape($_POST['till']);
        $teacher_employee_id = $db->escape($_POST['teacher_employee_id']);

        // Update subject data in the database
        $sql = "UPDATE subjects SET ";
        $sql .= "name='{$name}', semester='{$semester}', ";
        $sql .= "`from`='{$from}', ";
        $sql .= "`till`='{$till}', ";

        $sql .= "teacher_employee_id='{$teacher_employee_id}' ";
        $sql .= "WHERE id='{$subject_id}'";
        $result = $db->query($sql);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Subject updated successfully!');
            redirect('subjects.php', false);
        } else {
            $session->msg('d', 'Failed to update subject!');
            redirect('edit_subject.php?id=' . $subject_id, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_subject.php?id=' . $subject_id, false);
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
          <span>Edit Subject</span>
        </strong>
      </div>
      <div class="panel-body">
        <form action="edit_subject.php?id=<?php echo $subject_id; ?>" method="POST">
          <div class="form-group">
            <label for="subject_name">Subject Name:</label>
            <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?php echo remove_junk(ucwords($subject['name'])); ?>" required>
          </div>
          <div class="form-group">
            <label for="semester">Semester:</label>
            <select class="form-control" id="semester" name="semester" required>
              <option value="1st" <?php if ($subject['semester'] === '1st') echo 'selected'; ?>>1st</option>
              <option value="2nd" <?php if ($subject['semester'] === '2nd') echo 'selected'; ?>>2nd</option>
              <option value="3rd" <?php if ($subject['semester'] === '3rd') echo 'selected'; ?>>3rd</option>
            </select>
          </div>
          <div class="form-group">
            <label for="from">From:</label>
            <input type="time" class="form-control" id="from" name="from" value="<?php echo remove_junk($subject['from']); ?>" required>
          </div>
          <div class="form-group">
            <label for="till">Till:</label>
            <input type="time" class="form-control" id="till" name="till" value="<?php echo remove_junk($subject['till']); ?>" required>
          </div>
          <div class="form-group">
            <label for="teacher_employee_id">Teacher Employee ID:</label>
            <input type="text" class="form-control" id="teacher_employee_id" name="teacher_employee_id" value="<?php echo remove_junk($subject['teacher_employee_id']); ?>" required>
          </div>

          <button type="submit" class="btn btn-primary">Update Subject</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
