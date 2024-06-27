<?php
$page_title = 'All Assets';
require_once('includes/load.php');
// Checking what level user has permission to view this page
page_require_level(1);

$all_assets = find_all('assets');

if (isset($_POST['add_asset'])) {
    $req_field = array('asset_name');
    validate_fields($req_field);
    $asset_name = remove_junk($db->escape($_POST['asset_name']));
    
    // Append "-CHEMLAB" to the asset name
    $asset_name = $asset_name . "-CHEMLAB";

    if (empty($errors)) {
        $sql  = "INSERT INTO assets (asset_name)";
        $sql .= " VALUES ('{$asset_name}')";
        if ($db->query($sql)) {
            $session->msg("s", "Successfully Added New Asset");
            redirect('equipment_assets.php', false);
        } else {
            $session->msg("d", "Sorry Failed to insert.");
            redirect('equipment_assets.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('equipment_assets.php', false);
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
    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    <span>Add New Asset</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" name="asset_name" placeholder="Asset Name">
                            <span class="input-group-addon">-CHEMLAB</span>
                        </div>
                    </div>
                    <button type="submit" name="add_asset" class="btn btn-primary">Add Asset</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    <span class="glyphicon glyphicon-th"></span>
                    All Assets
                </strong>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Asset Name</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            foreach ($all_assets as $asset) :
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $count; ?></td>
                                <td><?php echo remove_junk(ucfirst($asset['asset_name'])); ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="edit_asset.php?id=<?php echo isset($asset['asset_id']) ? (int)$asset['asset_id'] : ''; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <a href="delete_asset.php?id=<?php echo isset($asset['asset_id']) ? (int)$asset['asset_id'] : ''; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            $count++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
