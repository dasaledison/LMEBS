<?php
$page_title = 'Home Page';
require_once('includes/load.php');
if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}
?>
<?php include_once('layouts/header.php'); ?>
<style>
  .custom-panel-body {
    min-height: 200px; /* Adjust the height as needed */
    padding: 20px; /* Add padding for better appearance */
  }

  .panel-body h2 {
    font-size: 24px; /* Adjust font size as needed */
    margin-top: 10px; /* Add top margin for spacing */
  }

  .panel-body span.glyphicon {
    font-size: 36px; /* Adjust the Glyphicon size as needed */
  }

  .panel-body p {
    font-size: 16px; /* Adjust font size as needed */
  }

  .panel-body a.btn {
    font-size: 18px; /* Adjust button font size as needed */
  }
</style>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
  <div class="col-md-12">
    <div class="panel">
      <div class="jumbotron text-center">
         <h1>Welcome to Laboratory Management and Equipment Reservation Portal </h1>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-body custom-panel-body">
        <h2 class="text-center"><span class="glyphicon glyphicon-pencil"></span> Reserve Equipment</h2>
        <p class="text-center">Reserve an equipment here</p>
        <a href="reserve_product_teachers.php" class="btn btn-primary btn-block"> Reserve Now</a>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-body custom-panel-body">
        <h2 class="text-center"><span class="glyphicon glyphicon-list-alt"></span> View Damage Reports</h2>
        <p class="text-center">View damage reports here</p>
        <a href="view_reports.php" class="btn btn-primary btn-block"> View Reports</a>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>