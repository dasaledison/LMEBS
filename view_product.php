<?php
$page_title = 'View Product';
require_once('includes/load.php');
require_once('includes/functions.php');

// Check the user's permission level
page_require_level(1);

// Check if product id is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];
    // Fetch product details from the database
    $product = find_product_by_id($product_id);
    if (!$product) {
        $session->msg('d', 'Product not found!');
        redirect('products.php');
    }
} else {
    $session->msg('d', 'Product ID is missing or invalid!');
    redirect('products.php');
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Product Details</h3>
                    
                </div>
                
                <div class="panel-body">
                <table class="table table-bordered">
    <tbody>
        <?php
            $identifier = $product['equipment_barcode'];

            // Generate the SVG data using JsBarcode
            $svgData = "<svg id='barcode_" . $product['id'] . "' class='barcode-svg' width='50' height='25'></svg><script>JsBarcode('#barcode_" . $product['id'] . "', '" . $identifier . "');</script>";
        ?>
        <tr>
            <th>Barcode:</th>
            <td><?php echo $svgData; ?>
        </td>
    
            
        </tr>
                                <th>Name:</th>
                                <td><?php echo $product['name']; ?></td>
                            </tr>
                            <tr>
                                <th>Quantity:</th>
                                <td><?php echo $product['quantity']; ?></td>
                            </tr>
                            <tr>
                                <th>In Use:</th>
                                <td><?php echo $product['in_use_qty']; ?></td>
                            </tr>
                            <tr>
                                <th>Unit:</th>
                                <td><?php echo $product['unit']; ?></td>
                            </tr>
                           
                            <tr>
                                <th>Date created:</th>
                                <td><?php echo $product['date']; ?></td>
                            </tr>
                       
                            <tr>
                                <th>Expected Quantity:</th>
                                <td><?php echo $product['expected_quantity']; ?></td>
                            </tr>
                            <tr>
                                <th>Item Number:</th>
                                <td><?php echo $product['item_num']; ?></td>
                            </tr>
                            <tr>
                                <th>Asset Number:</th>
                                <td><?php echo $product['asset_num']; ?></td>
                            </tr>
                            <tr>
                                <th>Brand:</th>
                                <td><?php echo $product['brand']; ?></td>
                            </tr>
                            <tr>
                                <th>Location:</th>
                                <td><?php echo $product['location']; ?></td>
                            </tr>
                            <tr>
                                <th>Description:</th>
                                <td><?php echo $product['description']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                <a href="product.php" class="btn btn-default">Back</a>
            </div>
            </div>
          
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3/dist/JsBarcode.all.min.js"></script>
<script>
    // Generate barcode
    var productId = <?php echo $product_id; ?>;
    JsBarcode("#barcode", productId, { format: "CODE128" });
</script>