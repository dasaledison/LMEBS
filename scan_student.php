<?php
// Page title and required files
$page_title = 'Scan Student';
require_once('includes/load.php');
// Check what level user has permission to view this page
page_require_level(1);

// Check if a student ID is scanned
if (isset($_GET['student-id'])) {
    // Retrieve student ID from the URL
    $student_id = $_GET['student-id'];
    // Check if the student ID exists in the database
    $student = find_student_by_id($student_id);
    if ($student) {
        // Student found, proceed to next page
        // Get the scanned items from the session
        if (isset($_SESSION['scanned_items'])) {
            $scanned_items = $_SESSION['scanned_items'];
            unset($_SESSION['scanned_items']); // Remove scanned items from session
        } else {
            $scanned_items = array(); // No scanned items
        }
        
        // Redirect to input_borrow_details.php with student ID and scanned items
        ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var url = "input_borrow_details.php";
                var separator = "?";
                var studentId = "<?php echo $student_id; ?>";
                var scannedItems = <?php echo json_encode($scanned_items); ?>;
                var queryParams = [];

                // Add student ID to the query parameters
                queryParams.push("student-id=" + studentId);

                // Add scanned items to the query parameters
                scannedItems.forEach(function(item) {
                    queryParams.push("equipment_barcode[]=" + encodeURIComponent(item['barcode']) + "&quantity[]=" + encodeURIComponent(item['quantity']));
                });

                // Construct the redirect URL
                var redirectUrl = url + separator + queryParams.join("&");

                // Redirect to the constructed URL
                window.location.href = redirectUrl;
            });
        </script>
        <?php
        exit(); // Stop further execution
    } else {
        // Student not found, display error message
        $session->msg('d', "Student ID not found.");
        echo "<script>window.history.back();</script>";
    }
}

function find_by_barcode($barcode) {
    global $db;
    $barcode = $db->escape($barcode);
    $sql = "SELECT * FROM products WHERE equipment_barcode = '{$barcode}' LIMIT 1";
    $result = $db->query($sql);
    if ($db->num_rows($result) > 0) {
        return $db->fetch_assoc($result);
    } else {
        return null;
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
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-camera"></span>
                    <span>Scan Student ID</span>
                </strong>
                <form id="teacher-form" class="pull-right">
                    <button type="button" onclick="submitTeacherForm()" class="btn btn-xs btn-success">Scan Teacher ID</button>
                </form>

                <script>
                    function submitTeacherForm() {
                        // Get all parameters from the current URL
                        var queryString = window.location.search;

                        // Redirect to scan_student.php with all parameters
                        window.location.href = "scan_teacher.php" + queryString;
                    }
                </script>
            </div>

            <div class="panel-body">
                <div id="reader" width="600px"></div>

                <script src="https://unpkg.com/html5-qrcode"></script>
                <script type="text/javascript">
                    // Function to handle form submission
                    $("#borrow-form").submit(function(event) {
                        event.preventDefault(); // Prevent the default form submission

                        // Get the student ID from the input field
                        var studentId = $("#student-id").val().trim();

                        // Validate input
                        if (studentId === "") {
                            alert("Please enter the student ID.");
                            return;
                        }

                        // Get the existing query string
                        var queryString = window.location.search.substring(1);

                        // Construct the URL with student ID and existing query string
                        var url = 'input_borrow_details.php?student-id=' + studentId + '&' + queryString;

                        // Redirect to input_borrow_details.php with student ID and existing query string
                        location.href = url;
                    });
                    function onScanSuccess(decodedText, decodedResult) {
    // handle the scanned code as you like, for example:
    console.log(`Code matched = ${decodedText}`, decodedResult);

    // Set the scanned student ID to the input field
    document.getElementById('student-id').value = decodedText;

    // AJAX request to check if student exists
    $.ajax({
        url: 'check_student.php',
        method: 'GET',
        data: { student_id: decodedText },
        dataType: 'json',
        success: function(response) {
            if (response.exists) {
                // Student exists, proceed with redirection
                var queryString = window.location.search.substring(1);
                var redirectUrl = 'input_borrow_details.php?student-id=' + decodedText + '&' + queryString;
                location.href = redirectUrl;
            } else {
                // Student not found, display alert
                alert("Student not found.");
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status, error);
            alert("An error occurred while checking the student. Please try again.");
        }
    });
}


                    function onScanFailure(error) {
                        // handle scan failure, usually better to ignore and keep scanning.
                        // for example:
                        console.warn(`Code scan error = ${error}`);
                    }

                    document.addEventListener("DOMContentLoaded", function() {
                        let html5QrcodeScanner = new Html5QrcodeScanner(
                            "reader",
                            { fps: 20, qrbox: { width: 600, height: 350 } },
                            /* verbose= */ false);
                        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                    });
                </script>

                <form id="borrow-form" method="get" action="" class="clearfix">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-list-alt"></i>
                            </span>
                            <input type="text" class="form-control" id="student-id" name="student-id" placeholder="Student ID" required>
                        </div>
                    </div>
                </form>
                <script type="text/javascript">
    // Function to handle form submission
    $("#borrow-form").submit(function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Get the student ID from the input field
        var studentId = $("#student-id").val().trim();

        // Validate input
        if (studentId === "") {
            alert("Please enter the student ID.");
            return;
        }

        // AJAX request to check if student exists
        $.ajax({
            url: 'check_student.php', // Change to the appropriate PHP file that checks student existence
            method: 'GET',
            data: {student_id: studentId},
            dataType: 'json',
            success: function(response) {
                if (response.exists) {
                    // Student exists, redirect to input_borrow_details.php
                    var queryString = window.location.search.substring(1);
                    var url = 'input_borrow_details.php?student-id=' + studentId + '&' + queryString;
                    window.location.href = url;
                } else {
                    // Student doesn't exist, display alert
                    alert("Student not found.");
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status, error);
                alert("An error occurred while checking the student. Please try again.");
            }
        });
    });
</script>

            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-list-alt"></span>
                    <span>Scanned Items</span>
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Equipment Name</th>
                            <th>Asset Number</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Check if equipment barcode and quantity are scanned and passed in the URL
                        if (isset($_GET['equipment_barcode'])) {
                            // Loop through each equipment barcode and quantity in the URL
                            $scanned_items = array();
                            parse_str($_SERVER['QUERY_STRING'], $query_params);
                            foreach ($query_params['equipment_barcode'] as $key => $barcode) {
                                $quantity = $query_params['quantity'][$key];
                                // Retrieve product information
                                $product = find_by_barcode($barcode);
                                if ($product) {
                                    // Display scanned item
                                    echo "<tr>";
                                    echo "<td>{$product['name']}</td>";
                                    echo "<td>{$product['asset_num']}</td>";
                                    echo "<td>{$quantity}</td>";
                                    echo "</tr>";
                                }
                            }
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
