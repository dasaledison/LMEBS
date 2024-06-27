<?php 
require_once('db-connect.php');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo "<script> alert('Error: No data to save.'); location.replace('./') </script>";
    $conn->close();
    exit;
}

extract($_POST);
$allday = isset($allday);

if (empty($id)) {
    // Insert into schedule_list
    $sqlSchedule = "INSERT INTO `schedule_list` (`title`, `description`, `start_datetime`, `end_datetime`) 
                    VALUES ('$title', '$description', '$start_datetime', '$end_datetime')";

    $saveSchedule = $conn->query($sqlSchedule);

    // Insert into student_reserved
    $reservation_status = 'approved'; // Set the reservation status to 'approved'
    $student_id = 123; // Replace with the actual student ID

    $sqlStudentReserved = "INSERT INTO `student_reserved` (`equipment`, `reservation_date_from`, `reservation_date_till`, `reservation_status`, `student_id`) 
                           VALUES ('$title', '$start_datetime', '$end_datetime', '$reservation_status', $student_id)";

    $saveStudentReserved = $conn->query($sqlStudentReserved);

    if ($saveSchedule && $saveStudentReserved) {
        echo "<script> alert('Schedule and Reservation Successfully Saved.'); location.replace('./') </script>";
    } else {
        handleSaveError($conn, $sqlSchedule, $sqlStudentReserved);
    }
} else {
    // Update schedule_list
    $sqlUpdateSchedule = "UPDATE `schedule_list` SET `title` = '$title', `description` = '$description', 
                         `start_datetime` = '$start_datetime', `end_datetime` = '$end_datetime' WHERE `id` = '$id'";

    $updateSchedule = $conn->query($sqlUpdateSchedule);

    // You might want to add logic to update the corresponding entry in student_reserved here

    if ($updateSchedule) {
        echo "<script> alert('Schedule Successfully Updated.'); location.replace('./') </script>";
    } else {
        handleSaveError($conn, $sqlUpdateSchedule);
    }
}

$conn->close();

function handleSaveError($conn, ...$queries) {
    echo "<pre>";
    echo "An Error occurred.<br>";
    foreach ($queries as $query) {
        echo "Error: " . $conn->error . "<br>";
        echo "SQL: " . $query . "<br>";
    }
    echo "</pre>";
}
?>
