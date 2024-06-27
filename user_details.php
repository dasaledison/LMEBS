<?php
// Include necessary files
require_once('includes/functions.php');
require_once('includes/load.php');

// Check user permission level if required

// Retrieve student ID from URL parameter
$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Retrieve student details based on the ID
$student_details = find_student_by_id($student_id);

// Include header file
include_once('layouts/header.php');
?>

<!-- HTML content for student details -->
<div class="container">
    <h2>Student Details</h2>
    <?php if ($student_details) : ?>
        <p>Name: <?php echo $student_details['name']; ?></p>
        <p>Email: <?php echo $student_details['email']; ?></p>
        <p>Department: <?php echo $student_details['department']; ?></p>
        <p>Course: <?php echo $student_details['course']; ?></p>
        <p>Section: <?php echo $student_details['section']; ?></p>
        <p>Student ID: <?php echo $student_details['student_id']; ?></p>
      
        <!-- Add more student details as needed -->
    <?php else : ?>
        <p>Student not found.</p>
    <?php endif; ?>
</div>

<?php
// Include footer file
include_once('layouts/footer.php');
?>
