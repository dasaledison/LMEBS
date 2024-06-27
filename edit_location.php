<?php
$page_title = 'Edit Location';
require_once('includes/load.php');
// Check What level user has permission to view this page
page_require_level(1);

// Find the location by its ID
$location = find_location_by_id((int)$_GET['id']);
if (!$location) {
    $session->msg("d", "Missing location ID.");
    redirect('equipment_locations.php');
}

if (isset($_POST['edit_location'])) {
    $req_field = array('room_num');
    validate_fields($req_field);
    $room_num = remove_junk($db->escape($_POST['room_num']));
    if (empty($errors)) {
        $sql = "UPDATE locations SET room_num='{$room_num}'";
        $sql .= " WHERE location_id='{$location['location_id']}'";
        $result = $db->query($sql);
        if ($result && $db->affected_rows() === 1) {
            $session->msg("s", "Successfully updated Location");
            redirect('equipment_locations.php', false);
        } else {
            $session->msg("d", "Sorry! Failed to Update");
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
   <div class="col-md-5">
     <div class="panel panel-default">
       <div class="panel-heading">
         <strong>
           <span class="glyphicon glyphicon-th"></span>
           <span>Editing <?php echo remove_junk(ucfirst($location['room_num']));?></span>
        </strong>
       </div>
       <div class="panel-body">
         <form method="post" action="edit_location.php?id=<?php echo (int)$location['location_id'];?>">
           <div class="form-group">
               <input type="text" class="form-control" name="room_num" value="<?php echo remove_junk(ucfirst($location['room_num']));?>">
           </div>
           <button type="submit" name="edit_location" class="btn btn-primary">Update Location</button>
       </form>
       </div>
     </div>
   </div>
</div>

<?php include_once('layouts/footer.php'); ?>
