<?php
// Include necessary files and start session if required
require_once('includes/load.php');
// Check user's permission level
page_require_level(1);

// Check if school ID is provided
if (isset($_GET['school_id'])) {
    // Retrieve scanned school ID
    $scanned_school_id = $_GET['school_id'];
    
    // Redirect to return_equipment_process.php with the scanned school ID
    header("Location: return_equipment_process.php?school_id=$scanned_school_id");
    exit; // Ensure no further execution of this script
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
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Scan School ID</span>
                </strong>
            </div>
            <div class="panel-body">
                <div id="reader" width="600px"></div>
                
                <script src="https://unpkg.com/html5-qrcode"></script>
                <script type="text/javascript">
                   function onScanSuccess(decodedText, decodedResult) {
    console.log(`Code matched = ${decodedText}`, decodedResult);
    // Set the scanned school ID to the input field
    document.getElementById('school-id').value = decodedText;
    // Submit the form
    document.getElementById('borrow-form').submit();
}


                    function onScanFailure(error) {
                        console.warn(`Code scan error = ${error}`);
                    }

                    let html5QrcodeScanner = new Html5QrcodeScanner(
                        "reader",
                        { fps: 20, qrbox: { width: 600, height: 350 } },
                        /* verbose= */ false);
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                </script>
    <form id="borrow-form" method="get" action="return_equipment_process.php">
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-list-alt"></i>
            </span>
            <!-- Change input name to 'school_id' -->
            <input type="text" class="form-control" id="school-id" name="school_id" placeholder="School ID" required>
        </div>
    </div>
</form>

            </div>
        </div>
    </div>
</div>

<?php
// Check if school ID is set in the URL
if(isset($_GET['school_id'])) {
    // Retrieve school ID from URL
    $scanned_school_id = $_GET['school_id'];
    // Output JavaScript to open the modal and pass the scanned school ID
    echo "<script>
            $(document).ready(function() {
                $('#scanned_school_id').val('$scanned_school_id');
                $('#returnModal').modal('show');
                
                // AJAX request to fetch borrower ID and equipment details
                $.ajax({
                    url: 'return_equipment.php',
                    method: 'GET',
                    data: {school_id: '$scanned_school_id'},
                    dataType: 'json',
                    success: function(response) {
                        // Fill borrower ID
                        $('#borrower_id').val(response.borrower_id);
                        // Fill equipment details
                        $('#equipment_details_body').empty();
                        $.each(response.equipment, function(index, item) {
                            $('#equipment_details_body').append(
                                '<tr>' +
                                '<td>' + item.asset_num + '</td>' +
                                '<td>' + item.quantity + '</td>' +
                                '</tr>'
                            );
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ' + status, error);
                    }
                });
            });
          </script>";
}
?>



<?php include_once('layouts/footer.php'); ?>