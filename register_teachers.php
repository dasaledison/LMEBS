<?php
ob_start();
require_once('includes/load.php');
if ($session->isUserLoggedIn(true)) { redirect('student_home.php', false); }
?>
<?php include_once('layouts/header.php'); ?>

<div class="login-page">
    <div class="text-center">
        <h1>Teacher Registration Panel</h1>
        <h4>NU LMERS</h4>
    </div>

    <?php
    $msg = isset($msg) ? $msg : '';
    echo display_msg($msg);
    ?>

    <form method="post" action="register_process.php" class="clearfix" id="registrationForm">
    <input type="hidden" name="user_type" value="teacher">

        <div class="form-group">
            <label for="name" class="control-label">Name</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Full Name" required>
        </div>

        <div class="form-group">
            <label for="email" class="control-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
        </div>

        <div class="form-group">
            <label for="password" class="control-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
        </div>

        <div class="form-group">
            <label for="confirmPassword" class="control-label">Confirm Password</label>
            <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" placeholder="Confirm Password" required>
        </div>

        <div class="form-group">
            <label for="employee_id" class="control-label">Employee ID</label>
            <input type="text" class="form-control" name="employee_id" id="employee_id" placeholder="Employee ID" required>
        </div>

        <div class="form-group" id="departmentFieldStudent">
            <label for="department" class="control-label">Department</label>
            <select class="form-control" name="department" id="department" required>
                <option value="College">College</option>
                <option value="SHS">SHS</option>
            </select>
        </div>

        <div class="form-group">
            <button type="submit" name="register_user" class="btn btn-danger" style="border-radius:0%">Register</button>
        </div>
    </form>
    <p>Are you a student? <a href="register_students.php">Register Here</a></p>
</div>

<?php include_once('layouts/footer.php'); ?>
