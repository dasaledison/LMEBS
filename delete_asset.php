<?php
require_once('includes/load.php');
// Check the user's permission level
page_require_level(1);

$asset = find_assets_by_id((int)$_GET['id']);
if (!$asset) {
  $session->msg("d", "Missing Asset ID.");
  redirect('equipment_assets.php');
}

$delete_id = delete_assets_by_id((int)$asset['asset_id']);
if ($delete_id) {
    $session->msg("s", "Asset deleted.");
    redirect('equipment_assets.php');
} else {
    $session->msg("d", "Asset deletion failed.");
    redirect('equipment_assets.php');
}
?>
