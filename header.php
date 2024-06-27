<?php $user = current_user(); ?>
<!DOCTYPE html>
  <html lang="en">
    <head>
    <meta charset="utf-8">
    <title><?php if (!empty($page_title))
           echo remove_junk($page_title);
            elseif(!empty($user))
           echo ucfirst($user['name']);
            else echo "NU LMERS";?>
    </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" integrity="sha256-4/VS7L3qBOk8zoq9hLaexA5OYIjZ0UKBm0E23XqTp6w=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <link rel="stylesheet" href="libs/css/main.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" integrity="sha256-n7lXb+3hqkYi+tf/Zx5Yz2lAu82f0aF7iCA4uy4VMOA=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha256-KJ3o2DKT7z5Y5fLsUVr3mPiuBKLwLaq+xTlVs6+9O4A=" crossorigin="anonymous" />
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src ="JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quagga/dist/quagga.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-nNEeaH8byvB9cdh4nN6SDy5eX8U3TlOA+dv7X5PWa+MqhebohPMcSUYMAI9XIR4Mab1eIJF+4zZB1/XBtY2y3Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  </head>
  <body>
  <?php  if ($session->isUserLoggedIn(true)): ?>
    <header id="header">
      <div class="logo pull-left"> NU LMERS</div>
      <div class="header-content">
      <div class="header-date pull-left">
      <?php
// Set the default time zone to UTC+08:00 (Philippine Standard Time)
date_default_timezone_set('Asia/Manila');

// Get the current date and time in UTC+08:00
$current_time = date("F j, Y, g:i a");

// Display the current date and time
echo '<strong>' . $current_time . '</strong>';
?>      </div>
      <div class="pull-right clearfix">
        <ul class="info-menu list-inline list-unstyled">
          <li class="profile">
            <a href="#" data-toggle="dropdown" class="toggle" aria-expanded="false">
              <img src="uploads/users/<?php echo $user['image'];?>" alt="user-image" class="img-circle img-inline">
              <span><?php echo remove_junk(ucfirst($user['name'])); ?> <i class="caret"></i></span>
            </a>
            <ul class="dropdown-menu">
              <li>
                  <a href="profile.php?id=<?php echo (int)$user['id'];?>">
                      <i class="glyphicon glyphicon-user"></i>
                      Profile
                  </a>
              </li>
             <li>
                 <a href="edit_account.php" title="edit account">
                     <i class="glyphicon glyphicon-cog"></i>
                     Settings
                 </a>
             </li>
             <li class="last">
                 <a href="logout.php">
                     <i class="glyphicon glyphicon-off"></i>
                     Logout
                 </a>
             </li>
           </ul>
          </li>
        </ul>
      </div>
     </div>
    </header>
    <div class="sidebar">
      <?php if($user['user_level'] === '1'): ?>
        <!-- admin menu -->
      <?php include_once('admin_menu.php');?>

      <?php elseif($user['user_level'] === '1'): ?>
        <!-- Special user -->
      <?php include_once('special_menu.php');?>

      <?php elseif($user['user_level'] === '1'): ?>
        <!-- User menu -->
      <?php include_once('user_menu.php');?>

      <?php elseif($user['user_level'] === '2'): ?>
        <!-- User menu -->
      <?php include_once('user_menu.php');?>

      <?php elseif($user['user_level'] === '3'): ?>
        <!-- User menu -->
      <?php include_once('user_menu_teachers.php');?>

      

      <?php endif;?>

   </div>
<?php endif;?>

<div class="page">
  <div class="container-fluid">
