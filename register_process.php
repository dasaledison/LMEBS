<?php
include_once('includes/load.php');
require_once('includes/sql.php'); // Make sure to include your SQL file

// Validate required fields
$req_fields = array('user_type', 'name', 'email', 'password', 'confirmPassword');
validate_fields($req_fields);

// Retrieve form data
$user_type = isset($_POST['user_type']) ? remove_junk($_POST['user_type']) : '';
$name = isset($_POST['name']) ? remove_junk($_POST['name']) : '';
$email = isset($_POST['email']) ? remove_junk($_POST['email']) : '';
$password = isset($_POST['password']) ? remove_junk($_POST['password']) : '';

// Validate password
if (strlen($password) < 8 || !preg_match("/[a-z]/i", $password) || !preg_match("/[0-9]/", $password)) {
    $session->msg("d", "Password must be at least 8 characters and contain at least one letter and one number.");
    redirect('register_students.php', false);
}

$confirmPassword = isset($_POST['confirmPassword']) ? remove_junk($_POST['confirmPassword']) : '';

// Additional fields for users
$student_id = $department = $course = $section = $employee_id = $user_id = ''; // Add these fields

if ($user_type === 'student') {
    $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : '';
    $department = isset($_POST['department']) ? remove_junk($_POST['department']) : '';
    $course = isset($_POST['course']) ? remove_junk($_POST['course']) : '';
    $section = isset($_POST['section']) ? remove_junk($_POST['section']) : '';
} elseif ($user_type === 'teacher') {
    $employee_id = isset($_POST['employee_id']) ? intval($_POST['employee_id']) : '';
    $department = isset($_POST['department']) ? remove_junk($_POST['department']) : '';
}

// Set default user level
$user_level = ($user_type === 'teacher') ? 3 : 2;

// Perform validation and registration
try {
    // Check if passwords match
    if ($password !== $confirmPassword) {
        throw new Exception("Passwords do not match.");
    }

    // Validate email domain based on user type
    if (($user_type === 'student' && !preg_match('/@students\.nu-laguna\.edu\.ph$/', $email)) ||
        ($user_type === 'teacher' && !preg_match('/@nu-laguna\.edu\.ph$/', $email))) {
        throw new Exception("Invalid email domain for $user_type.");
    }

    // Extract username from email address
    $username = substr($email, 0, strpos($email, '@'));

    // Hash the password
    $hashedPassword = sha1($password); // Hash the password (you may consider using a stronger hashing algorithm)

    // Perform registration based on user type
    if ($user_type === 'student') {
        register_user($name, $username, $email, $hashedPassword, $user_type, $user_level, $student_id, $department, $course, $section, null, null);
    } elseif ($user_type === 'teacher') {
        register_user($name, $username, $email, $hashedPassword, $user_type, $user_level, null, $department, null, null, $employee_id, null);
    } else {
        throw new Exception("Invalid user type.");
    }

    // Redirect on successful registration
    redirect('register_success.php', false);
} catch (Exception $e) {
    // Handle errors
    $session->msg("d", $e->getMessage());
    redirect('register_students.php', false); // Redirect back to the registration page with an error message
}

// Function for registering a user
function register_user($name, $username, $email, $password, $user_type, $user_level, $student_id = null, $department = null, $course = null, $section = null, $employee_id = null, $user_id = null)
{
    global $db;

    $sql = "INSERT INTO users (name, username, email, password, user_type, user_level, student_id, department, course, section, employee_id, user_id, status) 
            VALUES ('$name', '$username', '$email', '$password', '$user_type', '$user_level', '$student_id', '$department', '$course', '$section', '$employee_id', '$user_id', 1)";

    if ($db->query($sql)) {
        return true;
    } else {
        throw new Exception("Error: " . $db->error);
    }
}
?>
