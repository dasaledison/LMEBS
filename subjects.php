<?php
$page_title = 'Students';
require_once('includes/load.php');

// Check the user's permission level to view this page
page_require_level(2);

// Include the new function to fetch all students
require_once('includes/functions.php');

// Fetch all teachers from the database
$teachers = find_all_teachers();

// Fetch all students from the database
$students = find_all_students();
// Fetch all subjects from the database
$subjects = find_all_subjects();

// Handle CSV file upload
if (isset($_FILES['csv_file'])) {
    // Check if file upload was successful
    if ($_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        // Get the temporary file name
        $tmp_file = $_FILES['csv_file']['tmp_name'];

        // Process the uploaded CSV file
        $file_handle = fopen($tmp_file, 'r'); // Open the CSV file for reading
        
        // Skip the header row
        fgetcsv($file_handle);

        // Read each line from the CSV file and insert into the database
        while (($data = fgetcsv($file_handle)) !== false) {
            // Extract data from the CSV row
            $name = $data[0];
            $email = $data[1];
            $department = $data[2];
            $course = $data[3];
            $section = $data[4];
            $student_id = $data[5];
            
            // Insert the data into the students table
            $insert_student = insert_student($name, $email, $department, $course, $section, $student_id);

            // Check if the insertion was successful
            if ($insert_student) {
                $msg = "CSV file uploaded successfully. Data inserted into the database.";
            } else {
                $msg = "Error inserting data into the database.";
            }
        }

        fclose($file_handle); // Close the CSV file

    } else {
        // File upload failed, provide an error message
        $msg = "Error uploading CSV file.";
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
  <div class="col-md-12">
    <!-- Display subject data here -->
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Subjects</span>
        </strong>
        <div class="pull-right">
        <a href="add_subject.php"  class="btn btn-success btn-sm m-1">
    <span class="glyphicon glyphicon-plus" style="color: #ffffff;"></span> Add Subject
</a>
        </div>
      </div>
      <div class="panel-body">
      <div style="max-height: 300px; overflow: auto;">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Name</th>
                <th>Semester</th>
                <th>Schedule</th>
                <th>Time</th>
                <th>Teacher</th> <!-- Add the teacher column -->
                <th>Actions</th> <!-- Add this column for edit and delete buttons -->
              </tr>
            </thead>
            <tbody>
              <?php foreach ($subjects as $subject): ?>
                <?php 
                    // Check if any day is set to 1
                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    $schedule = [];
                    foreach ($days as $day) {
                        if ($subject[$day] == 1) {
                            $schedule[] = ucfirst($day);
                        }
                    }
                    $schedule_str = implode(' / ', $schedule);
                    // Construct the time range
                    $time_range = $subject['from'] . ' - ' . $subject['till'];
                    // Only display subjects with at least one day set to 1
                    if (!empty($schedule)): 
                ?>
                <tr>
                  <td><?php echo $subject['name']; ?></td>
                  <td><?php echo $subject['semester']; ?></td>
                  <td><?php echo $schedule_str; ?></td>
                  <td><?php echo $time_range; ?></td>
                  <td><?php echo $subject['teacher_employee_id']; ?></td> <!-- Display the teacher -->
                  <td>
                    <a href="edit_subject.php?id=<?php echo $subject['id']; ?>" class="btn" style="background-color: transparent;"><span class="glyphicon glyphicon-edit" style="color: #007bff;"></span></a> <!-- Edit button -->
                    <a href="delete_subject.php?id=<?php echo $subject['id']; ?>" class="btn" style="background-color: transparent;"><span class="glyphicon glyphicon-trash" style="color: #dc3545;"></span></a> <!-- Delete button -->
                    <a href="add_students.php?subject_id=<?php echo $subject['id']; ?>" class="btn ">
    <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
</a>
                  </td>
                  <!-- Add the button for adding students -->
                </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

</div>

<?php include_once('layouts/footer.php'); ?>
