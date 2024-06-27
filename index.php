<?php
ob_start();
require_once('includes/load.php');

// Check if the user is already logged in
if ($session->isUserLoggedIn(true)) {
    // Get the user ID
    $user_id = $_SESSION['user_id'];

    // Fetch the user level from the database
    global $db;
    $user_level_query = "SELECT user_level FROM users WHERE id = {$user_id} LIMIT 1";
    
    // Use the query method to execute the SQL query
    $result = $db->query($user_level_query);

    if ($result) {
        $user_level = $result->fetch_assoc()['user_level'];

        // Redirect based on user level
        if ($user_level == '1') {
          $session->msg("s", "Welcome to Laboratory Management and Equipment Reservation System!");
            redirect('labmanager_home.php', false);
        } elseif ($user_level == '2'){
          $session->msg("s", "Welcome to Laboratory Management and Equipment Reservation System!");
            redirect('student_home.php', false);
        } elseif ($user_level == '3') {
          $session->msg("s", "Welcome to Laboratory Management and Equipment Reservation System!");
            redirect('teacher_home.php', false);
        } else {
            // Redirect to a default page or handle as needed
            redirect('default_home.php', false);
        }
    } else {
        // Handle the case where user level retrieval fails
        // Redirect to a default page or handle as needed
        redirect('default_home.php', false);
    }
} else {
    // Continue with the rest of your code for non-logged-in users
}

?>
<?php include_once('layouts/header.php'); ?>
<div class="login-page">
    <div class="text-center">
    <img class="img-size-3"src="libs/images/LOGO.png" alt="National University Logo">
       <h2>Login Panel</h2>
            </div>
            &nbsp;
     <?php echo display_msg($msg); ?>
      <form method="post" action="auth.php" class="clearfix">
        <div class="form-group">
              <label for="username" class="control-label">Username</label>
              <input type="name" class="form-control" name="username" placeholder="Username">
        </div>
        <div class="form-group">
            <label for="Password" class="control-label">Password</label>
            <input type="password" name= "password" class="form-control" placeholder="Password">
        </div>
        <div class="form-group">
                <button type="submit" class="btn btn-danger" style="border-radius:0%">Login</button>
       
           <!--   <a href="register_students.php" class="btn btn-danger" style="border-radius:0%">Register</a> -->  
        </div>
    </form>
</div>
<?php include_once('layouts/footer.php'); ?>