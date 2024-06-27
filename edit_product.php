<?php
$page_title = 'Edit Product';
require_once('includes/load.php');

// Check the user's permission level to view this page
page_require_level(1);

// Fetch product details by ID
$product = find_by_id('products', (int)$_GET['id']);
$all_assets = find_all('assets');

// If the product is not found, redirect to the product list page
if (!$product) {
    $session->msg("d", "Missing product ID.");
    redirect('product.php');
}

// Process the form submission
if (isset($_POST['product'])) {
    // Define required fields
    $req_fields = array('product-title', 'product-quantity', 'product-asset', 'product-asset-num', 'product-item-num', 'product-unit', 'product-expected-quantity');
    validate_fields($req_fields);

    if (empty($errors)) {
        // Fetch input data
        $p_qty = (int)$_POST['product-quantity'];
        $expected_quantity = remove_junk($db->escape($_POST['product-expected-quantity']));
        $asset_name = remove_junk($db->escape($_POST['product-asset']));
        $asset_num = remove_junk($db->escape($_POST['product-asset-num']));

        // Check if the asset name already contains the asset number prefix
        $asset_num = (strpos($asset_name, $asset_num) === false) ? $asset_name . '-' . $asset_num : $asset_name;

        $p_name = remove_junk($db->escape($_POST['product-title']));

        // Check if the product name already exists
        $existing_product = find_by_column('products', 'name', $p_name);

        if ($existing_product && $existing_product['id'] != $product['id']) {
            // Product with the same name already exists
            $session->msg('d', 'Equipment with the same name already exists.');
            redirect('edit_product.php?id=' . $product['id'], false);
        }

        // Check if the asset number already exists
        $existing_asset = find_by_column('products', 'asset_num', $asset_num);
        if ($existing_asset && $existing_asset['id'] != $product['id']) {
            // Asset number already exists
            $session->msg('d', 'Asset number already exists.');
            redirect('edit_product.php?id=' . $product['id'], false);
        }

        // Check if the checkbox for deleting photo is checked
        $delete_photo = isset($_POST['delete-photo']) ? (int)$_POST['delete-photo'] : 0;

        if ($delete_photo && !empty($product['product_img'])) {
            // Delete the existing photo from the server
            $photo_path = 'uploads/products/' . $product['product_img'];
            if (file_exists($photo_path)) {
                unlink($photo_path);
            }

            // Remove the photo reference from the database
            $query_remove_photo = "UPDATE products SET product_img = NULL WHERE id = '{$product['id']}'";
            $db->query($query_remove_photo);
        }

        // Handle product photo upload
        if (isset($_FILES['product-photo']) && $_FILES['product-photo']['error'] === UPLOAD_ERR_OK) {
            // New photo is uploaded
            $product_img = upload_product_photo($_FILES['product-photo']);
            if (!$product_img) {
                $session->msg('d', 'Failed to upload product photo.');
                redirect('edit_product.php?id=' . $product['id'], false);
            }
        } else {
            // No new photo uploaded, use the existing one
            $product_img = $product['product_img'];
        }

        // Additional fields
        $unit = remove_junk($db->escape($_POST['product-unit']));
        $item_num = remove_junk($db->escape($_POST['product-item-num']));
        $brand = remove_junk($db->escape($_POST['product-brand']));
        $location = remove_junk($db->escape($_POST['product-location']));
        $description = remove_junk($db->escape($_POST['product-description']));

        // Update product information in the database
        $query  = "UPDATE products SET";
        $query .= " name = '{$p_name}', asset_num = '{$asset_num}', quantity = '{$p_qty}',";
        $query .= " unit = '{$unit}', expected_quantity = '{$expected_quantity}', item_num = '{$item_num}',";
        $query .= " brand = '{$brand}', location = '{$location}', description = '{$description}',";
        $query .= " product_img = '{$product_img}'";
        $query .= " WHERE id = '{$product['id']}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'Product updated successfully.');
            redirect('product.php', false);
        } else {
            $session->msg('d', 'Failed to update product.');
            redirect('product.php?id=' . $product['id'], false);
        }
    } else {
        $session->msg('d', $errors);
        redirect('edit_product.php?id=' . $product['id'], false);
    }
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    Update Product
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="edit_product.php?id=<?php echo (int)$product['id']; ?>" class="clearfix" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="product-title" class="control-label">Title</label>
                        <input type="text" class="form-control" name="product-title" placeholder="Product Title" value="<?php echo remove_junk($product['name']); ?>">
                    </div>
                 
                    <div class="form-group">
                        <label for="product-quantity" class="control-label">Quantity</label>
                        <input type="number" class="form-control" name="product-quantity" placeholder="Product Quantity" value="<?php echo remove_junk($product['quantity']); ?>">
                    </div>
                    <div class="form-group">
    <label for="product-expected-quantity" class="control-label">Expected Quantity</label>
    <input type="number" class="form-control" name="product-expected-quantity" placeholder="Expected Quantity" required>
</div>

                    <!-- Additional form fields -->
                    <div class="form-group">
                        <label for="product-asset" class="control-label">Asset</label>
                        <select class="form-control" name="product-asset">
                            <?php foreach ($all_assets as $asset) : ?>
                                <option value="<?php echo remove_junk($asset['asset_name']); ?>" <?php if (isset($product['asset_name']) && $product['asset_name'] == $asset['asset_name']) echo 'selected'; ?>>
                                    <?php echo remove_junk($asset['asset_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="product-asset-num" placeholder="Number" value="<?php echo preg_replace('/[^0-9]/', '', remove_junk($product['asset_num'])); ?>">
                    </div>
                    <div class="form-group">
                        <label for="product-brand" class="control-label">Brand</label>
                        <input type="text" class="form-control" name="product-brand" placeholder="Brand" value="<?php echo remove_junk($product['brand']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="product-item-num" class="control-label">Item Number</label>
                        <input type="text" class="form-control" name="product-item-num" placeholder="Item Number" value="<?php echo remove_junk($product['item_num']); ?>">
                    </div>
                    <div class="form-group">
    <label for="product-unit" class="control-label">Unit</label>
    <select class="form-control" name="product-unit">
        <?php
        $units = array(
            'Boxes', 'Pcs', 'Kits', 'Sets', 'Dozen',
            'Pack', 'Case', 'Carton', 'Pairs', 'Bundles'
        );

        $selected_unit = remove_junk($product['unit']); // Get the selected unit from the product data
        foreach ($units as $unit) {
            // Loop to generate options with unit titles
            $selected = ($selected_unit == $unit) ? 'selected' : ''; // Set 'selected' attribute for the current unit
            echo "<option value='{$unit}' {$selected}>{$unit}</option>";
        }
        ?>
    </select>
</div>

                    <!-- End of new form fields -->

                    <div class="form-group">
                        <label for="product-location" class="control-label">Location</label>
                        <select class="form-control" name="product-location">
                            <?php
                            // Fetch locations from the database
                            $all_locations = find_all('locations');
                            foreach ($all_locations as $location) :
                            ?>
                                <option value="<?php echo remove_junk($location['room_num']); ?>" <?php if ($product['location'] == $location['room_num']) echo 'selected'; ?>>
                                    <?php echo remove_junk($location['room_num']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="product-description" class="control-label">Description</label>
                        <textarea class="form-control" name="product-description" placeholder="Description"><?php echo remove_junk($product['description']); ?></textarea>
                    </div>
                    <!-- End of new form fields -->

                    <button type="submit" name="product" class="btn btn-danger">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once('layouts/footer.php'); ?>
