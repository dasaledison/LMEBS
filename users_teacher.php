<?php
$page_title = 'Admin Users';
require_once('includes/load.php');

// Check the user's permission level to view this page
page_require_level(2);

// Fetch admin users with user_level = 3 from the database
$admin_users = find_all_user(3);
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Teachers</span>
        </strong>
        <div class="btn-group pull-right" style="margin-right: 10px;">
          <a href="users.php" class="btn btn-info">All</a>
          <a href="users_admin.php" class="btn btn-info">Admin</a>
          <a href="users_student.php" class="btn btn-info">Student</a>
          <a href="users_teacher.php" class="btn btn-info">Teacher</a>
          <span style="margin-right: 5px;"></span>
          <a href="add_user.php" class="btn btn-success">
            <span class="glyphicon glyphicon-plus"></span> Add New User
          </a>
        </div>
      </div>

      <div class="panel-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Name</th>
              <th>Username</th>
              <th class="text-center" style="width: 15%;">User Role</th>
              <th class="text-center" style="width: 10%;">Status</th>
              <th style="width: 20%;">Last Login</th>
              <th class="text-center" style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($admin_users as $admin_user): ?>
              <?php if ($admin_user['user_level'] == 3): ?>
                <tr>
                  <td class="text-center"><?= count_id(); ?></td>
                  <td><?= remove_junk(ucwords($admin_user['name'])); ?></td>
                  <td><?= remove_junk(ucwords($admin_user['username'])); ?></td>
                  <td class="text-center"><?= remove_junk(ucwords($admin_user['group_name'])); ?></td>
                  <td class="text-center">
                    <span class="label <?= ($admin_user['status'] === '1') ? 'label-success' : 'label-danger'; ?>">
                      <?= ($admin_user['status'] === '1') ? 'Active' : 'Deactive'; ?>
                    </span>
                  </td>
                  <td><?= read_date($admin_user['last_login']); ?></td>
                  <td class="text-center">
                    <div class="btn-group">
                      <a href="edit_user.php?id=<?= (int) $admin_user['id']; ?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                        <i class="glyphicon glyphicon-pencil"></i>
                      </a>
                      <a href="delete_user.php?id=<?= (int) $admin_user['id']; ?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                        <i class="glyphicon glyphicon-remove"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
