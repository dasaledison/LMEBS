<?php
$errors = array();

/*--------------------------------------------------------------*/
/* Function for Remove escapes special
/* characters in a string for use in an SQL statement
/*--------------------------------------------------------------*/
function real_escape($str){
  global $con;
  $escape = mysqli_real_escape_string($con,$str);
  return $escape;
}

/*--------------------------------------------------------------*/
/* Function for Remove html characters
/*--------------------------------------------------------------*/
function remove_junk($str){
  $str = nl2br($str);
  $str = htmlspecialchars(strip_tags($str, ENT_QUOTES));
  return $str;
}

/*--------------------------------------------------------------*/
/* Function for Uppercase first character
/*--------------------------------------------------------------*/
function first_character($str){
  $val = str_replace('-'," ",$str);
  $val = ucfirst($val);
  return $val;
}

/*--------------------------------------------------------------*/
/* Function for Checking input fields not empty
/*--------------------------------------------------------------*/
function validate_fields($var){
  global $errors;
  foreach ($var as $field) {
    $val = remove_junk($_POST[$field]);
    if(isset($val) && $val==''){
      $errors = $field ." can't be blank.";
      return $errors;
    }
  }
}

/*--------------------------------------------------------------*/
/* Function for Display Session Message
   Ex echo displayt_msg($message);
/*--------------------------------------------------------------*/
function display_msg($msg =''){
   $output = array();
   if(!empty($msg)) {
      foreach ($msg as $key => $value) {
         $output[]  = "<div class=\"alert alert-{$key}\">";
         $output[]  = "<a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>";
         $output[]  = remove_junk(first_character($value));
         $output[]  = "</div>";
      }
      return implode("", $output);
   } else {
     return "" ;
   }
}
function get_quantity_for_item($asset_num) {
    global $db;

    $sql = "SELECT quantity FROM all_borrowers WHERE asset_num = '{$asset_num}' AND status = 'In Use' LIMIT 1";
    $result = $db->query($sql);

    if ($result && $db->num_rows($result) == 1) {
        return $db->fetch_assoc($result)['quantity'];
    } else {
        return 0;
    }
}

function insert_subject($subject_name, $semester, $from, $till, $days) {
  global $db;

  // Construct the SQL query
  $sql = "INSERT INTO subjects (name, semester, `from`, till, monday, tuesday, wednesday, thursday, friday, saturday, sunday) 
          VALUES ('$subject_name', '$semester', '$from', '$till', '{$days[0]}', '{$days[1]}', '{$days[2]}', '{$days[3]}', '{$days[4]}', '{$days[5]}', '{$days[6]}')";

  // Execute the query
  $result = $db->query($sql);

  // Check if the query was successful
  if ($result) {
      return true;
  } else {
      return false;
  }
}
function find_subjects_by_teacher_id($employee_id) {
    global $db;
  
    // Sanitize the input to prevent SQL injection
    $employee_id = $db->escape($employee_id);
  
    // Query to find subjects associated with the teacher
    $query = "SELECT * FROM subjects 
              WHERE teacher_employee_id = '$employee_id'";
  
    // Execute the query
    $result = $db->query($query);
  
    // Initialize an empty array to store subjects
    $subjects = array();
  
    // Check if the query was successful
    if ($result && $db->num_rows($result) > 0) {
        // Fetch each row as an associative array
        while ($row = $db->fetch_assoc($result)) {
            $subjects[] = $row;
        }
    }
  
    return $subjects;
}

// includes/functions.php
function find_subjects_by_student_id($student_id) {
  global $db;

  // Sanitize the input to prevent SQL injection
  $student_id = $db->escape($student_id);

  // Query to find subjects associated with the student
  $query = "SELECT s.* FROM subjects s 
            JOIN student_subjects ss ON s.id = ss.subject_id 
            WHERE ss.student_id = '$student_id'";

  // Execute the query
  $result = $db->query($query);

  // Initialize an empty array to store subjects
  $subjects = array();

  // Check if the query was successful
  if ($result && $db->num_rows($result) > 0) {
      // Fetch each row as an associative array
      while ($row = $db->fetch_assoc($result)) {
          $subjects[] = $row;
      }
  }

  return $subjects;
}
function find_damage_reports_by_status($status)
{
    global $db;
    // Modify the query based on your database schema
    $sql = "SELECT * FROM borrower_damage_report WHERE report_status = '{$status}'";
    $result = $db->query($sql);
    $reports = [];
    while ($row = $db->fetch_assoc($result)) {
        $reports[] = $row;
    }
    return $reports;
}
function find_by_borrower_id($table, $borrower_id) {
    global $db;
    $result = $db->query("SELECT * FROM {$table} WHERE borrower_id = '{$borrower_id}' LIMIT 1");
    return $db->fetch_assoc($result);
}
function find_borrow_details_by_borrower_id($borrower_id) {
    global $db;
    $sql = "SELECT * FROM all_borrowers WHERE borrower_id = '{$borrower_id}' LIMIT 1";
    $result = $db->query($sql);
    return $db->fetch_assoc($result);
}
function find_report_by_report_id($table, $report_id) {
    global $db;
    $sql = "SELECT * FROM {$db->escape($table)} WHERE report_id = '{$db->escape($report_id)}' LIMIT 1";
    $result_set = $db->query($sql);
    return $db->fetch_assoc($result_set);
}
function get_borrow_details_by_borrower_id($borrower_id) {
    global $db;

    // Prepare the SQL query to fetch required details
    $query = "SELECT school_id, borrowed_from, borrowed_till, subject_id
              FROM all_borrowers
              WHERE borrower_id = '$borrower_id'
              LIMIT 1"; // Limit to retrieve only the first row

    // Execute the query
    $result = $db->query($query);

    // Check if the query was successful
    if ($result) {
        // Fetch the row
        $row = $result->fetch_assoc();
        $result->free(); // Free the result set
        
        // Remove or comment out the debug output
        // var_dump($row); // Output the retrieved row
        
        return $row;
    } else {
        return null; // Query failed or no rows found
    }
}

function generate_borrower_id() {
    // Generate a random number between 100000 and 999999 (inclusive)
    return mt_rand(100000, 999999);
}
function get_equipment_and_quantity_by_borrower_id($borrower_id) {
    global $db;
    $borrower_id = $db->escape($borrower_id);
    $sql = "SELECT asset_num, quantity FROM all_borrowers WHERE borrower_id = '{$borrower_id}'";
    $result = $db->query($sql);
    $items = array();
    while ($row = $db->fetch_assoc($result)) {
        $items[] = $row;
    }
    return $items;
}
function find_student_by_id($student_id) {
  global $db;

  // Sanitize the input to prevent SQL injection
  $student_id = $db->escape($student_id);

  // Query to find the student by ID
  $query = "SELECT * FROM students WHERE student_id = '{$student_id}' LIMIT 1";

  // Execute the query
  $result = $db->query($query);

  // Check if the query was successful and if the student was found
  if ($result && $db->num_rows($result) > 0) {
      // Fetch the student as an associative array
      $student = $db->fetch_assoc($result);
      return $student;
  } else {
      // Student not found
      return null;
  }
}
// Define a function to check if a student ID already exists in the database
function student_id_exists($student_id) {
    global $db; // Assuming $db is your database connection object

    // Query to check if the student ID exists
    $query = "SELECT COUNT(*) AS total FROM students WHERE student_id = '$student_id'";
    $result = $db->query($query);

    // Check if any rows were found with the given student ID
    $row = $result->fetch_assoc();
    return ($row['total'] > 0);
}

function find_teacher_by_id($employee_id) {
    global $db;
  
    // Sanitize the input to prevent SQL injection
    $teacher_id = $db->escape($employee_id);
  
    // Query to find the teacher by ID
    $query = "SELECT * FROM teachers WHERE employee_id = '{$employee_id}' LIMIT 1";
  
    // Execute the query
    $result = $db->query($query);
  
    // Check if the query was successful and if the teacher was found
    if ($result && $db->num_rows($result) > 0) {
        // Fetch the teacher as an associative array
        $teacher = $db->fetch_assoc($result);
        return $teacher;
    } else {
        // Teacher not found
        return null;
    }
  }
  
// Function to find 'from' and 'till' values by subject_id
function find_from_till_by_subject_id($subject_id) {
  global $db; // Assuming $db is your database connection object

  // Sanitize the subject_id to prevent SQL injection
  $subject_id = $db->escape($subject_id);

  // SQL query to select 'from' and 'till' from subjects table based on subject_id
  $sql = "SELECT `from`, `till` FROM subjects WHERE subject_id = '$subject_id'";

  // Execute the query
  $result = $db->query($sql);

  // Check if query was successful and if any rows were returned
  if ($result && $db->num_rows($result) > 0) {
      // Fetch the row as an associative array
      $row = $db->fetch_assoc($result);
      // Return an associative array containing 'from' and 'till' values
      return array(
          'from' => $row['from'],
          'till' => $row['till']
      );
  } else {
      // No rows found, return false
      return false;
  }
}
// Assuming you have a database connection established
// Assuming you have a database connection established

// Assuming you have a database connection established

function find_teacher_by_employee_id($employee_id) {
    global $db; // Assuming $db is your database connection

    // Query to fetch teacher details based on the provided employee ID
    $query = "SELECT * FROM teachers WHERE employee_id = {$employee_id}";

    // Execute the query
    $result = $db->query($query);

    // Check if the query was successful
    if (!$result) {
        // Handle the error, if any
        die("Database query failed: " . $db->error);
    }

    // Fetch the teacher details
    $teacher_details = $result->fetch_assoc();

    // Free the result set
    $result->free();

    // Return the teacher details
    return $teacher_details;
}

// Function to fetch subjects taught by the teacher with the provided employee ID

// Function to find subject ID by student ID
function find_subject_id_by_student_id($student_id) {
    global $db; // Assuming $db is your database connection object

    // Sanitize the student ID to prevent SQL injection
    $student_id = intval($student_id);

    // Query to fetch subject ID based on student ID
    $query = "SELECT subject_id FROM your_subjects_table WHERE student_id = {$student_id}";

    // Execute the query
    $result = $db->query($query);

    // Check if query was successful
    if ($result && $result->num_rows == 1) {
        // Fetch the subject ID
        $row = $result->fetch_assoc();
        return $row['subject_id'];
    } else {
        // Query failed or subject not found
        return null;
    }
}
function find_borrow_by_id($borrow_id) {
    global $db; // Assuming $db is your database connection object

    // Sanitize the borrow ID to prevent SQL injection
    $borrow_id = intval($borrow_id);

    // Query to fetch borrow details based on borrow ID
    $query = "SELECT * FROM all_borrowers WHERE borrow_id = {$borrow_id}";

    // Execute the query
    $result = $db->query($query);

    // Check if query was successful
    if ($result && $result->num_rows == 1) {
        // Fetch the borrow details
        $row = $result->fetch_assoc();
        return $row;
    } else {
        // Query failed or borrow not found
        return null;
    }
}
// Function to find product details by asset number
function find_product_by_asset_num($asset_num) {
    global $db; // Assuming $db is your database connection

    // Escape the asset number to prevent SQL injection
    $asset_num = $db->escape($asset_num);

    // Query to fetch product details based on asset number
    $query = "SELECT * FROM products WHERE asset_num = '{$asset_num}' LIMIT 1";

    // Execute the query
    $result = $db->query($query);

    // Check if the query was successful and if a row was returned
    if ($result && $db->num_rows($result) > 0) {
        // Fetch the product details
        $product = $db->fetch_assoc($result);
        return $product;
    } else {
        // Product not found
        return null;
    }
}
// Function to find subject details by subject ID
function find_subject_by_id($subject_id) {
    global $db; // Assuming $db is your database connection

    // Escape the subject ID to prevent SQL injection
    $subject_id = $db->escape($subject_id);

    // Query to fetch subject details based on subject ID
    $query = "SELECT * FROM subjects WHERE id = '{$subject_id}' LIMIT 1";

    // Execute the query
    $result = $db->query($query);

    // Check if the query was successful and if a row was returned
    if ($result && $db->num_rows($result) > 0) {
        // Fetch the subject details
        $subject = $db->fetch_assoc($result);
        return $subject;
    } else {
        // Subject not found
        return null;
    }
}
function find_all_subjects() {
  global $db;
  $sql = "SELECT * FROM subjects";
  $result = $db->query($sql);
  $subjects = [];
  while ($row = $db->fetch_assoc($result)) {
      $subjects[] = $row;
  }
  return $subjects;
}
function insert_teacher($name, $email, $department, $employee_id)
{
    global $db;
    
    // Escape values to prevent SQL injection
    $name = $db->escape($name);
    $email = $db->escape($email);
    $department = $db->escape($department);
    $employee_id = $db->escape($employee_id);
    
    // Query to insert teacher into the database
    $query = "INSERT INTO teachers (name, email, department, employee_id) VALUES ('{$name}', '{$email}', '{$department}', '{$employee_id}')";
    
    // Execute the query
    if ($db->query($query)) {
        // If insertion was successful, return true
        return true;
    } else {
        // If insertion failed, return false
        return false;
    }
}

function find_classes_by_teacher_id($teacher_id)
{
    global $db;

    $teacher_id = (int)$teacher_id;

    $sql = "SELECT * FROM subjects WHERE teacher_employee_id = '{$teacher_id}'";
    $result = $db->query($sql);

    $subjects = [];
    while ($row = $db->fetch_assoc($result)) {
        $subjects[] = $row;
    }

    return $subjects;
}
function search_borrowers($search_term) {
    global $db;
    $safe_search = mysqli_real_escape_string($db->con, $search_term);
    $query = "SELECT * FROM all_borrowers WHERE school_id LIKE '%$safe_search%' OR subject_id LIKE '%$safe_search%' OR asset_num LIKE '%$safe_search%'";
    $result = find_by_sql($query);
    return $result;
}
// Function to find all student damage reports
function find_all_student_damage_reports() {
    return find_by_sql("SELECT * FROM student_damage_reports");
}
function find_student_info_by_borrow_id($borrow_id) {
    global $db; // Assuming $db is your database connection object

    $sql = "SELECT school_id FROM all_borrowers WHERE borrow_id = '$borrow_id'";
    $result = $db->query($sql);

    // Check if query was successful
    if ($result) {
        $student_info = $result->fetch_assoc();
        return $student_info;
    } else {
        return null; // Return null if no student information found
    }
}
function find_by_report_id($table, $id) {
    global $db;
    $id = (int)$id;
    if ($table === 'borrower_damage_report' || $table === 'lab_damage_reports') {
        $sql = "SELECT * FROM {$db->escape($table)} WHERE id='{$id}' LIMIT 1";
        $result = $db->query($sql);
        return $db->fetch_assoc($result);
    }
    // Handle other tables if needed
    return null;
}


// Function to find all other damage reports grouped by cause
function find_all_other_damage_reports_grouped_by_cause() {
    global $db;
    $sql = "SELECT * FROM other_damage_reports GROUP BY cause";
    $result_set = $db->query($sql);
    $results = array();
    while ($row = $db->fetch_assoc($result_set)) {
        $results[] = $row;
    }
    return $results;
}

// Function to retrieve all borrowers from the database
function find_all_borrowers() {
    return find_all('all_borrowers');
}
// Function to insert a student into a subject
function insert_student_subject($student_id, $subject_id) {
  global $db;

  // Sanitize inputs
  $student_id = (int)$db->escape($student_id);
  $subject_id = (int)$db->escape($subject_id);

  // Check if the student is already enrolled in the subject
  $sql_check = "SELECT * FROM student_subjects WHERE student_id = {$student_id} AND subject_id = {$subject_id}";
  $result_check = $db->query($sql_check);
  if ($db->num_rows($result_check) > 0) {
      // Student is already enrolled in the subject, return false
      return false;
  }

  // Insert the student into the subject
  $sql_insert = "INSERT INTO student_subjects (student_id, subject_id) VALUES ({$student_id}, {$subject_id})";
  $result_insert = $db->query($sql_insert);

  // Check if the insertion was successful
  if ($result_insert) {
      return true;
  } else {
      // Error inserting data
      echo "Error: " . $db->error(); // Debugging statement
      return false;
  }
}
function find_all_teachers() {
    global $db;
    $sql = "SELECT * FROM teachers";
    return find_by_sql($sql);
}
function find_student_subjects($student_id) {
  global $db;

  // Sanitize the input to prevent SQL injection
  $student_id = $db->escape($student_id);

  // Query to find subjects associated with the student
  $query = "SELECT * FROM student_subjects WHERE student_id = '$student_id'";

  // Execute the query
  $result = $db->query($query);

  // Initialize an empty array to store subjects
  $subjects = array();

  // Check if the query was successful
  if ($result && $db->num_rows($result) > 0) {
      // Fetch each row as an associative array
      while ($row = $db->fetch_assoc($result)) {
          $subjects[] = $row;
      }
  }

  return $subjects;
}



// Function to fetch students enrolled in a subject by subject ID
// Function to fetch students enrolled in a subject by subject ID
function find_students_by_subject_id($subject_id) {
  global $db;

  // Sanitize the subject ID
  $subject_id = (int)$db->escape($subject_id);

  // Query to fetch students enrolled in the subject
  $sql = "SELECT students.* FROM students 
          INNER JOIN student_subjects ON students.student_id = student_subjects.student_id 
          WHERE student_subjects.subject_id = {$subject_id}";

  // Execute the query
  $result = $db->query($sql);

  // Check if the query was successful
  if ($result) {
      // Fetch the result as an associative array
      $students = [];
      while ($row = $db->fetch_assoc($result)) {
          $students[] = $row;
      }
      return $students;
  } else {
      // Error executing query
      echo "Error: " . $db->error(); // Debugging statement
      return []; // Return an empty array if no students found
  }
}




/*--------------------------------------------------------------*/
/* Function for redirect
/*--------------------------------------------------------------*/
function redirect($url, $permanent = false)
{
    if (headers_sent() === false)
    {
      header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
    }

    exit();
}

/*--------------------------------------------------------------*/
/* Function for Readable date time
/*--------------------------------------------------------------*/
function read_date($str){
     if($str)
      return date('F j, Y, g:i:s a', strtotime($str));
     else
      return null;
}

/*--------------------------------------------------------------*/
/* Function for  Readable Make date time
/*--------------------------------------------------------------*/
function make_date(){
  return strftime("%Y-%m-%d %H:%M:%S", time());
}

/*--------------------------------------------------------------*/
/* Function for  Readable date time
/*--------------------------------------------------------------*/
function count_id(){
  static $count = 1;
  return $count++;
}

/*--------------------------------------------------------------*/
/* Function for Creting random string
/*--------------------------------------------------------------*/
function randString($length = 5)
{
  $str='';
  $cha = "0123456789abcdefghijklmnopqrstuvwxyz";

  for($x=0; $x<$length; $x++)
   $str .= $cha[mt_rand(0,strlen($cha))];
  return $str;
}

function upload_product_photo($file)
{
    $photo_dir = 'uploads/products/';
    $target_file = $photo_dir . basename($file['name']);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($target_file)) {
        return false;
    }

    // Check file size (adjust as needed)
    if ($file['size'] > 500000) {
        return false;
    }

    // Allow certain file formats
    if ($imageFileType != 'jpg' && $imageFileType != 'jpeg' && $imageFileType != 'png') {
        return false;
    }

    // Move the file to the specified directory
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return basename($file['name']);
    } else {
        return false;
    }
}

function find_by_column($table, $column, $value) {
  global $db;
  $value = $db->escape($value);
  $result = $db->query("SELECT * FROM {$table} WHERE {$column} = '{$value}' LIMIT 1");
  return $db->fetch_assoc($result);
}

function update_by_id($table, $id, $data) {
  global $db;

  $update_values = array();
  foreach ($data as $key => $value) {
      $update_values[] = "{$key}='{$value}'";
  }

  $sql = "UPDATE {$table} SET " . implode(', ', $update_values) . " WHERE id='{$id}'";
  $result = $db->query($sql);

  return $result;
}
function insert_into_equipment_barcode($productId, $barcodeFilePath) {
  global $db;

  $sql = "UPDATE products SET equipment_barcode = ? WHERE id = ?";
  $stmt = $db->prepare($sql);

  if (!$stmt) {
      // Handle prepare error
      return false;
  }

  $stmt->bind_param("ss", $barcodeFilePath, $productId);

  if (!$stmt->execute()) {
      // Handle execute error
      return false;
  }

  $stmt->close();
  return true;
}

function update_student_barcode($studentId, $barcode) {
  global $db;

  $sql = "UPDATE students SET student_barcode = ? WHERE student_id = ?";
  $stmt = $db->prepare($sql);

  if (!$stmt) {
      // Handle prepare error
      return false;
  }

  $stmt->bind_param("ss", $barcode, $studentId);

  if (!$stmt->execute()) {
      // Handle execute error
      return false;
  }

  $stmt->close();
  return true;
}
function find_product_by_equipment_barcode($barcode) {
  global $db;

  // Sanitize the input to prevent SQL injection
  $barcode = $db->escape($barcode);

  // Query to find a product by equipment barcode
  $result = $db->query("SELECT * FROM products WHERE equipment_barcode = '{$barcode}' LIMIT 1");

  // Check if the query was successful
  if ($result->num_rows > 0) {
      // Fetch the product details as an associative array
      return $db->fetch_assoc($result);
  } else {
      return false; // Product not found
  }
}

function find_product_name_by_equipment_barcode($barcode) {
  global $db;

  // Sanitize the input to prevent SQL injection
  $barcode = $db->escape($barcode);

  // Query to find the product name by equipment barcode
  $result = $db->query("SELECT name FROM products WHERE equipment_barcode = '{$barcode}' LIMIT 1");

  // Check if the query was successful
  if ($result->num_rows > 0) {
      // Fetch the product name
      $row = $db->fetch_assoc($result);
      return $row['name'];
  } else {
      return false; // Product not found
  }
}


function find_all_students() {
  global $db;

  // Perform database query to fetch all students
  $sql = "SELECT * FROM students";
  $result = $db->query($sql);

  // Check if the query was successful
  if ($result) {
      return $result->fetch_all(MYSQLI_ASSOC);
  } else {
      // Query failed, return an empty array
      return [];
  }
}
function find_subject_id($student_id) {
  global $db;

  // Sanitize the input to prevent SQL injection
  $student_id = (int)$db->escape($student_id);

  // Query to find the subject ID
  $query = "SELECT subject_id FROM student_subjects WHERE student_id = '{$student_id}' LIMIT 1";

  // Execute the query
  $result = $db->query($query);

  // Check if the query was successful
  if ($result && $result->num_rows > 0) {
      // Fetch the subject ID
      $row = $result->fetch_assoc();
      return $row['subject_id'];
  } else {
      return false; // Subject ID not found
  }
}
function get_reserved_events() {
  global $db; // Assuming $db is your database connection object

  $query = "SELECT * FROM student_reserved";
  $result = $db->query($query);

  $events = array();
  while ($row = $result->fetch_assoc()) {
      $events[] = array(
          "equipment" => $row['equipment'],
          "reservation_date_from" => $row['reservation_date_from'],
          "reservation_date_till" => $row['reservation_date_till']
      );
  }

  return $events;
}

function insert_student($name, $email, $department, $course, $section, $student_id) {
  global $db;

  // Sanitize the data to prevent SQL injection
  $name = $db->escape($name);
  $email = $db->escape($email);
  $department = $db->escape($department);
  $course = $db->escape($course);
  $section = $db->escape($section);
  $student_id = $db->escape($student_id);

  // Construct the SQL query to insert data into the students table
  $sql = "INSERT INTO students (name, email, department, course, section, student_id) ";
  $sql .= "VALUES ('{$name}', '{$email}', '{$department}', '{$course}', '{$section}', '{$student_id}')";

  try {
      // Execute the query
      if ($db->query($sql)) {
          // Insertion successful
          return true;
      } else {
          // Insertion failed
          return false;
      }
  } catch (mysqli_sql_exception $e) {
      // Duplicate entry error
      $_SESSION['msg'] = "Email '{$email}' already exists.";
      return false;
  }
}

function upload_file($file_input_name, $upload_dir, $allowed_formats = ["jpg", "jpeg", "png", "gif"]) {
  if (!isset($_FILES[$file_input_name])) {
      return false;
  }

  $file = $_FILES[$file_input_name];

  // Check for upload errors
  if ($file['error'] !== UPLOAD_ERR_OK) {
      return false;
  }

  // Check file format
  $file_info = pathinfo($file['name']);
  $file_ext = strtolower($file_info['extension']);
  if (!in_array($file_ext, $allowed_formats)) {
      return false;
  }

  // Generate a unique file name
  $new_filename = uniqid() . '.' . $file_ext;
  $target_file = $upload_dir . $new_filename;

  // Move the uploaded file to the target directory
  if (move_uploaded_file($file['tmp_name'], $target_file)) {
      return $new_filename;
  } else {
      return false;
  }
}
// Function to generate dummy events for testing
class BarcodeGenerator {
  public static function generateBarcodeSVG($studentId) {
      // Generate barcode using JsBarcode library
      $svg = '<svg id="barcode_' . $studentId . '"></svg>';
      echo '<script>generateBarcode("' . $studentId . '")</script>';
      return $svg;
  }
}
function get_dummy_events() {
  return [
      [
          "equipment" => "Laptop",
          "reservation_date_from" => "2024-02-20",
          "reservation_date_till" => "2024-02-22"
      ],
      [
          "equipment" => "Projector",
          "reservation_date_from" => "2024-02-25",
          "reservation_date_till" => "2024-02-27"
      ]
      // Add more dummy data as needed
  ];
}

// Add this function in your PHP code
function get_reservation_details($reservation_id) {
  global $db;
  $sql = "SELECT * FROM student_reserved WHERE id = '{$reservation_id}' LIMIT 1";
  $result = $db->query($sql);

  if ($result && $db->num_rows($result) == 1) {
      return $db->fetch_assoc($result);
  } else {
      return false;
  }
}

function get_teacher_reservation($reservation_id, $teacher_id) {
    global $db;
    $sql = "SELECT * FROM teacher_reserved WHERE id = '{$reservation_id}' AND user_id = '{$teacher_id}' LIMIT 1";
    $result = $db->query($sql);

    if ($result && $db->num_rows($result) == 1) {
        return $db->fetch_assoc($result);
    } else {
        return false;
    }
  }
  class TeacherReservationHandler {
  
    // Function to get reservation details by ID
    public function getReservationDetails($reservationId) {
        global $db;
        $reservationId = $db->escape($reservationId);
        $sql = "SELECT * FROM teacher_reserved WHERE id = {$reservationId} LIMIT 1";
        $result = $db->query($sql);
        
        if ($result && $db->num_rows($result) == 1) {
            return $db->fetch_assoc($result);
        } else {
            return false;
        }
    }
  }
  
class ReservationHandler {
  
  // ... your existing class code ...

  // Function to get reservation details by ID
  public function getReservationDetails($reservationId) {
      global $db;
      $reservationId = $db->escape($reservationId);
      $sql = "SELECT * FROM student_reserved WHERE id = {$reservationId} LIMIT 1";
      $result = $db->query($sql);
      
      if ($result && $db->num_rows($result) == 1) {
          return $db->fetch_assoc($result);
      } else {
          return false;
      }
  }

  // Function to decrease product quantity when reservation is approved
  public function decreaseProductQuantity($productId, $quantity) {
      global $db;
      $productId = $db->escape($productId);
      $quantity = $db->escape($quantity);
      $sql = "UPDATE products SET quantity = quantity - {$quantity} WHERE id = {$productId}";
      return $db->query($sql);
  }

  // Function to increase product quantity when an approved reservation is canceled
  public function increaseProductQuantity($productId, $quantity) {
      global $db;
      $productId = $db->escape($productId);
      $quantity = $db->escape($quantity);
      $sql = "UPDATE products SET quantity = quantity + {$quantity} WHERE id = {$productId}";
      return $db->query($sql);
  }
}

  
function updateReservationStatus()
{
    // Initialize the database connection
    global $db;

    // Update reservations for students
    $sql_student = "UPDATE student_reserved 
                    SET reservation_status = 
                        CASE
                            WHEN reservation_date_till < NOW() THEN 'Completed'
                            WHEN reservation_date_from <= NOW() AND reservation_date_till >= NOW() AND reservation_status = 'Approved' THEN 'In Use'
                            WHEN reservation_status = 'Pending' AND reservation_date_from < NOW() THEN 'Cancelled'
                            ELSE reservation_status
                        END";

    // Update reservations for teachers
    $sql_teacher = "UPDATE teacher_reserved 
                    SET reservation_status = 
                        CASE
                            WHEN reservation_date_till < NOW() THEN 'Completed'
                            WHEN reservation_date_from <= NOW() AND reservation_date_till >= NOW() AND reservation_status = 'Approved' THEN 'In Use'
                            WHEN reservation_status = 'Pending' AND reservation_date_from < NOW() THEN 'Cancelled'
                            ELSE reservation_status
                        END";

    // Execute the queries
    $db->query($sql_student);
    $db->query($sql_teacher);

    // Mark reservations as 'Completed' if their end time has passed
    $sql_completed = "UPDATE student_reserved 
                      SET reservation_status = 'Completed' 
                      WHERE reservation_status <> 'Completed' AND reservation_date_till < NOW()";
    $db->query($sql_completed);

    $sql_completed_teacher = "UPDATE teacher_reserved 
                              SET reservation_status = 'Completed' 
                              WHERE reservation_status <> 'Completed' AND reservation_date_till < NOW()";
    $db->query($sql_completed_teacher);
}

function find_product_by_id($id)
{
    global $db; // Assuming $db is your database connection object

    // Sanitize the input to prevent SQL injection
    $id = intval($id);

    // Query to fetch product details by ID
    $query = "SELECT * FROM products WHERE id = {$id} LIMIT 1";

    // Execute the query
    $result = $db->query($query);

    // Check if the query executed successfully
    if ($result && $db->num_rows($result) > 0) {
        // Fetch product details as an associative array
        $product = $db->fetch_assoc($result);
        return $product;
    } else {
        // Product not found
        return null;
    }
}
// Function to find subject details by subject ID
function find_subject_by_subject_id($subject_id) {
  global $db;

  // Sanitize the input to prevent SQL injection
  $subject_id = $db->escape($subject_id);

  // Query to fetch subject details based on subject ID
  $query = "SELECT * FROM subjects WHERE subject_id = '$subject_id'";

  // Execute the query
  $result = $db->query($query);

  // Check if the query was successful and if subject exists
  if ($result && $db->num_rows($result) > 0) {
      // Fetch subject details as an associative array
      $subject_details = $db->fetch_assoc($result);
      return $subject_details;
  } else {
      // Subject not found
      return false;
  }
}



function find_all_products_with_category() {
  global $db;

  $sql = "SELECT p.*, c.name AS category_name 
          FROM products p
          LEFT JOIN categories c ON p.categorie_id = c.id";

  return find_by_sql($sql);
}

// Function to find all confirmed student damaged reports
function find_all_student_damaged_reports()
{
    global $db; // Assuming $db is your database connection variable

    // Modify the SQL query according to your actual database structure
    $sql = "SELECT * FROM student_damaged_reports WHERE report_status = 'Confirmed'";

    $result = $db->query($sql);

    // Check if the query was successful
    if ($result) {
        $reports = [];

        // Fetch each row from the result set
        while ($row = $db->fetch_assoc($result)) {
            $reports[] = $row;
        }

        return $reports;
    } else {
        // Query failed
        return false;
    }
}

// Function to find all confirmed teachers damaged reports
function find_all_teachers_damaged_reports()
{
    global $db; // Assuming $db is your database connection variable

    // Modify the SQL query according to your actual database structure
    $sql = "SELECT * FROM teachers_damaged_reports WHERE report_status = 'Confirmed'";

    $result = $db->query($sql);

    // Check if the query was successful
    if ($result) {
        $reports = [];

        // Fetch each row from the result set
        while ($row = $db->fetch_assoc($result)) {
            $reports[] = $row;
        }

        return $reports;
    } else {
        // Query failed
        return false;
    }
}

function update($table, $data, $where) {
  global $db;

  $update_values = array();
  foreach ($data as $column => $value) {
      $update_values[] = "{$column} = '{$value}'";
  }

  $where_values = array();
  foreach ($where as $column => $value) {
      $where_values[] = "{$column} = '{$value}'";
  }

  $sql = "UPDATE {$table} SET " . implode(', ', $update_values) . " WHERE " . implode(' AND ', $where_values);

  $result = $db->query($sql);

  return $result;

  // Additional code to handle specific cases for damaged_reports_students
  if ($table == 'student_damaged_reports' && isset($data['quantity'])) {
      // Assuming 'product_id' is a column in 'student_damaged_reports' table
      $product_id = $where['product_id'];
      $quantity = $data['quantity'];

      // Update the quantity in the 'products' table
      update('products', ['quantity' => "quantity - {$quantity}"], ['id' => $product_id]);
  }
}
// Call the function to update reservation status
// Assuming your database connection is established in includes/load.php
function redirect_to($location) {
    header("Location: {$location}");
    exit();
}
function find_users_by_group($group_id) {
  global $db;

  $sql = "SELECT * FROM users WHERE user_level = {$group_id}";
  $result = find_by_sql($sql);

  return $result;
}

?>