
<?php
$page_title = 'All locations';
require_once('includes/load.php');
// Checking what level user has permission to view this page
page_require_level(1);

$all_locations = find_all('locations');

if (isset($_POST['add_location'])) {
    $req_field = array('room_num');
    validate_fields($req_field);
    $room_num = remove_junk($db->escape($_POST['room_num']));
    if (empty($errors)) {
        $sql  = "INSERT INTO locations (room_num)";
        $sql .= " VALUES ('{$room_num}')";
        if ($db->query($sql)) {
            $session->msg("s", "Successfully Added New Location");
            redirect('equipment_locations.php', false);
        } else {
            $session->msg("d", "Sorry Failed to insert.");
            redirect('equipment_locations.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('equipment_locations.php', false);
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
                    <span>Add New Location</span>
                </strong>
            </div>
            <div class="panel-body">
                <form method="post" action="">
                    <div class="form-group">
                        <input type="text" class="form-control" name="room_num" placeholder="Room Number">
                    </div>
                    <button type="submit" name="add_location" class="btn btn-primary">Add Location</button>
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
                    All Locations
                </strong>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Room Number</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            foreach ($all_locations as $loc) :
                            ?>
                           <tr>
    <td class="text-center"><?php echo $count; ?></td>
    <td><?php echo remove_junk(ucfirst($loc['room_num'])); ?></td>
    <td class="text-center">
        <div class="btn-group">
            <a href="edit_location.php?id=<?php echo isset($loc['location_id']) ? (int)$loc['location_id'] : ''; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                <span class="glyphicon glyphicon-edit"></span>
            </a>
            <a href="delete_location.php?id=<?php echo isset($loc['location_id']) ? (int)$loc['location_id'] : ''; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
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
