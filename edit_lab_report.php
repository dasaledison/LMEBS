<?php
require_once('includes/load.php');
page_require_level(1); // Requires admin level for accessing this page

// Check if report ID is provided via GET request
if (!isset($_GET['id'])) {
    $session->msg("d", "Report ID not provided.");
    redirect('damage_reports.php'); // Adjust as needed
}

$id = (int)$_GET['id'];

// Find the report data
$sql = "SELECT * FROM lab_damage_reports WHERE id = '{$db->escape($id)}' LIMIT 1";
$report_result = $db->query($sql);

if ($db->num_rows($report_result) === 0) {
    $session->msg("d", "Report not found.");
    redirect('all_other_reports.php'); // Adjust as needed
}

$report = $db->fetch_assoc($report_result);

// Fetch all products from the database
$sql = "SELECT name, asset_num FROM products";
$result = $db->query($sql);
$products = [];
while ($row = $db->fetch_assoc($result)) {
    $products[] = $row;
}

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
                <form action="edit_lab_report_process.php" method="post" enctype="multipart/form-data">
                    <!-- Product Dropdown -->
                    <label for="product">Select Equipment:</label><br>
                    <select name="asset_num" class="form-control" required>
                        <option value="">Select Equipment</option>
                        <?php foreach ($products as $product) : ?>
                            <option value="<?php echo $product['asset_num']; ?>" <?php echo $product['asset_num'] == $report['asset_num'] ? 'selected' : ''; ?>><?php echo $product['name'] . ' (' . $product['asset_num'] . ')'; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <br>

                    <!-- Quantity -->
                    <label for="quantity">Quantity:</label><br>
                    <input type="number" class="form-control" name="quantity" value="<?php echo $report['quantity']; ?>" required>

                    <br>

                    <div class="input-group">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-picture"></span>
                        </span>
                        <input type="file" class="form-control" name="picture[]" id="fileInput" accept="image/*">
                    </div>
                    <label id="fileInputLabel" for="fileInput">Click to Upload a Picture</label>
                    <div id="image_preview">
                        <?php if (!empty($report['picture'])) : ?>
                            <img src="uploads/<?php echo $report['picture']; ?>" alt="Report Image" style="max-width: 200px;">
                        <?php endif; ?>
                    </div>

                    <br>
                    <!-- Cause of Damage -->
                    <label for="cause">Cause of Damage:</label><br>
                    <input type="text" class="form-control" name="cause" value="<?php echo $report['cause']; ?>" required>

                    <!-- Notes -->
                    <label for="notes">Notes:</label><br>
                    <textarea name="notes" class="form-control" rows="6" cols="50" required><?php echo $report['notes']; ?></textarea><br>

                    <!-- Report status dropdown -->
             

                    <!-- Report ID -->
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

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
?>
