<?php
  ob_start();
  require_once('includes/load.php');
  if($session->isUserLoggedIn(true)) { redirect('student_home.php', false);}
?>
<?php include_once('layouts/header.php'); ?>

<div class="login-page">
    <div class="text-center">
        <h1>Registration Panel</h1>
        <h4>NU LMERS</h4>
    </div>

    
    <h3>Registration Success!</h3>
    <a href="index.php" class="btn btn-danger" style="border-radius:0%">Login Here</a>


<?php include_once('layouts/footer.php'); ?>