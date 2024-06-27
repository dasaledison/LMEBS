<?php include_once('includes/load.php'); ?>
<?php include_once('layouts/header.php'); ?>

<div class="login-page">
    <div class="text-center">
        <h1>Student Registration</h1>
        <h4>NU LMERS</h4>
    </div>

    <?php echo display_msg($msg); ?>

    <form method="post" action="register_process.php" class="clearfix" id="registrationForm">

        <!-- Hidden field for user_type -->
        <input type="hidden" name="user_type" value="student">

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

        <!-- Additional fields for students -->
        <div class="form-group" id="student_idField">
            <label for="student_id" class="control-label">Student ID Number</label>
            <input type="text" class="form-control" name="student_id" id="student_id" placeholder="Student ID" required>
        </div>

        <div class="form-group" id="departmentFieldStudent">
            <label for="department" class="control-label">Department</label>
            <select class="form-control" name="department" id="department" required>
                <option value="College">College</option>
                <option value="SHS">SHS</option>
            </select>
        </div>

        <!-- Course and section fields for students -->
        <div class="form-group" id="courseStrandField">
            <label for="course" class="control-label">Course/Strand</label>
            <input type="text" class="form-control" name="course" id="course" placeholder="Course/Strand" required>
        </div>

        <div class="form-group" id="sectionField">
            <label for="section" class="control-label">Section</label>
            <input type="text" class="form-control" name="section" id="section" placeholder="Section" required>
        </div>

        <div class="form-group">
            <button type="submit" name="register_user" class="btn btn-danger" style="border-radius:0%">Register</button>
        
        </div>
    </form>
    <p>Are you a teacher? <a href="register_teachers.php">Register Here</a></p>
</div>

<?php include_once('layouts/footer.php'); ?>
