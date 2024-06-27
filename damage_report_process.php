<?php
require_once('includes/load.php');
page_require_level(1); // Requires admin level for accessing this page

// Check if borrower_id is provided
if (isset($_GET['borrower_id'])) {
    $borrower_id = $_GET['borrower_id'];

    // Fetch rows with bad_condition > 0 for the given borrower_id
    $sql = "SELECT * FROM all_borrowers WHERE borrower_id = '$borrower_id' AND bad_condition > 0";
    $result = $db->query($sql);
    if (!$result) {
        // Handle error
        $session->msg('d', 'Error fetching damaged items.');
        redirect('damage_report.php'); // Redirect to damage_report.php or any other appropriate page
    }
    
    // Process the fetched rows here

    // Display the form
    include_once('layouts/header.php');
    ?>
    <style>
        #image_preview img {
            max-width: 200px; /* Set maximum width for the displayed image */
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <?php echo display_msg($msg); ?>
        </div>
    </div>

    <!-- Damage Report Form -->
   <!-- Damage Report Form -->
<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Damage Report Form</span>
                </strong>
            </div>
            <div class="panel-body">
                <form action="damage_report_action.php" method="post" enctype="multipart/form-data">
                    <p><strong>Borrower ID:</strong> <?php echo $borrower_id; ?></p>
                    <label for="associated_assets">Reporting for the following equipment:</label><br>
                    <ul>
                        <?php
                        // Fetch rows with bad_condition > 0 for the given borrower_id
                        $sql = "SELECT asset_num, bad_condition FROM all_borrowers WHERE borrower_id = '$borrower_id' AND bad_condition > 0";
                        $result = $db->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<li>" . $row['asset_num'] . " (Quantity: " . $row['bad_condition'] . ")</li>";
                            }
                        } else {
                            echo "<li>No assets with bad condition found.</li>";
                        }
                        ?>
                    </ul>
                    <br>

                    <div class="input-group">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-picture"></span>
                        </span>
                        <input type="file" class="form-control" name="picture[]" id="fileInput" accept="image/*" required>
                    </div>
                    <label id="fileInputLabel" for="fileInput">Click to Upload a Picture</label>
                    <div id="image_preview"></div>

                    <br>
                    <!-- Cause of Damage -->
                    <label for="cause_of_damage">Cause of Damage:</label><br>
                    <input type="text" class="form-control" name="cause_of_damage" required>

                    <!-- Notes -->
                    <label for="notes">Notes:</label><br>
                    <textarea name="notes" class="form-control" rows="6" cols="50" required></textarea><br>

                    <!-- Report status dropdown -->
                    <label for="report_status">Report Status:</label><br>
                    <select name="report_status" class="form-control" required>
                        <option value="Confirmed">Confirmed</option>
                        <option value="Resolved">Resolved</option>
                    </select><br>

                    <!-- Hidden field to store borrower_id -->
                    <input type="hidden" class="form-control" name="borrower_id" value="<?php echo $borrower_id; ?>">

                    <!-- Submit button -->
                    <input type="submit" class="btn btn-success" value="Submit">
                </form>
            </div>
        </div>
    </div>
</div>


    
    <!-- Script for previewing uploaded images -->
    <script>
        function previewImages() {
            var preview = document.getElementById('image_preview');
            preview.innerHTML = '';
            if (this.files) {
                [].forEach.call(this.files, readAndPreview);
            }

            function readAndPreview(file) {
                // Make sure `file` is an image
                if (!/\.(jpe?g|png|gif)$/i.test(file.name)) {
                    return alert(file.name + " is not an image");
                }
                var reader = new FileReader();
                reader.addEventListener("load", function () {
                    var image = new Image();
                    image.title = file.name;
                    image.src = this.result;
                    var filename = document.createElement('p');
                    filename.textContent = file.name;
                    preview.appendChild(image);
                    preview.appendChild(filename);
                });
                reader.readAsDataURL(file);
            }
        }

        document.getElementById('fileInput').addEventListener("change", previewImages);
    </script>
    <?php
    include_once('layouts/footer.php');
} else {
    // If borrower_id is not provided
    $session->msg('d', 'Borrower ID is missing.');
    redirect('damage_report.php'); // Redirect to damage_report.php or any other appropriate page
}
?>
