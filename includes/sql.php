<?php
require_once('includes/load.php');

/*--------------------------------------------------------------*/
/* Function for find all database table rows by table name
/*--------------------------------------------------------------*/
function find_all($table) {
   global $db;
   if(tableExists($table))
   {
     return find_by_sql("SELECT * FROM ".$db->escape($table));
   }
}
/*--------------------------------------------------------------*/
/* Function for Perform queries
/*--------------------------------------------------------------*/
function find_by_sql($sql)
{
  global $db;
  $result = $db->query($sql);
  if ($result) {
      return $db->while_loop($result);
  } else {
      return false;
  }
}
/*--------------------------------------------------------------*/
/*  Function for Find data from table by id
/*--------------------------------------------------------------*/
function find_by_id($table,$id)
{
  global $db;
  $id = (int)$id;
    if(tableExists($table)){
          $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE id='{$db->escape($id)}' LIMIT 1");
          if($sql) {
              if ($result = $db->fetch_assoc($sql)) {
                  return $result;
              } else {
                  return null;
              }
          } else {
              return null;
          }
     }
}
function find_location_by_id($id)
{
    global $db;
    $id = (int)$id;
    $table = 'locations'; // Assuming the table name is 'locations'

    if (tableExists($table)) {
        $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE location_id='{$db->escape($id)}' LIMIT 1");
        if ($sql) {
            if ($result = $db->fetch_assoc($sql)) {
                return $result;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
function find_assets_by_id($id)
{
    global $db;
    $id = (int)$id;
    $table = 'assets'; // Assuming the table name is 'assets'

    if (tableExists($table)) {
        $sql = $db->query("SELECT * FROM {$table} WHERE asset_id='{$id}' LIMIT 1");
        if ($sql) {
            if ($result = $db->fetch_assoc($sql)) {
                return $result;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}


/*--------------------------------------------------------------*/
/* Function for Delete data from table by id
/*--------------------------------------------------------------*/
function delete_by_id($table,$id)
{
  global $db;
  if(tableExists($table))
   {
    $sql = "DELETE FROM ".$db->escape($table);
    $sql .= " WHERE id=". $db->escape($id);
    $sql .= " LIMIT 1";
    $result = $db->query($sql);
    return ($db->affected_rows() === 1) ? true : false;
   }
}
function delete_location_by_id($id)
{
    global $db;
    $id = (int)$id;
    $table = 'locations'; // Assuming the table name is 'locations'

    if (tableExists($table)) {
        $sql = "DELETE FROM {$db->escape($table)} WHERE location_id = '{$db->escape($id)}' LIMIT 1";
        $result = $db->query($sql);
        return ($db->affected_rows() === 1) ? true : false;
    }
}
function delete_assets_by_id($id)
{
    global $db;
    $id = (int)$id;
    $table = 'assets'; // Assuming the table name is 'locations'

    if (tableExists($table)) {
        $sql = "DELETE FROM {$db->escape($table)} WHERE asset_id = '{$db->escape($id)}' LIMIT 1";
        $result = $db->query($sql);
        return ($db->affected_rows() === 1) ? true : false;
    }
}

/*--------------------------------------------------------------*/
/* Function for Count id  By table name
/*--------------------------------------------------------------*/
function get_approved_reservations() {
  global $db; // Assuming $db is your database connection

  $query = "SELECT id, equipment, reservation_date_from AS start, reservation_date_till AS end FROM student_reserved WHERE reservation_status = 'Approved'";
  $result = $db->query($query);

  $reservations = array();
  while ($row = $result->fetch_assoc()) {
      $reservations[] = $row;
  }

  return $reservations;
}

function count_by_id($table){
  global $db;
  if(tableExists($table))
  {
    $sql    = "SELECT COUNT(id) AS total FROM ".$db->escape($table);
    $result = $db->query($sql);
    if ($result) {
        return $db->fetch_assoc($result);
    } else {
        return false;
    }
  }
}
/*--------------------------------------------------------------*/
/* Determine if database table exists
/*--------------------------------------------------------------*/
function tableExists($table){
  global $db;
  $table_exit = $db->query('SHOW TABLES FROM '.DB_NAME.' LIKE "'.$db->escape($table).'"');
      if($table_exit) {
        if($db->num_rows($table_exit) > 0)
              return true;
         else
              return false;
      }
  }
 /*--------------------------------------------------------------*/
 /* Login with the data provided in $_POST,
 /* coming from the login form.
/*--------------------------------------------------------------*/
  function authenticate($username='', $password='') {
    global $db;
    $username = $db->escape($username);
    $password = $db->escape($password);
    $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
    $result = $db->query($sql);
    if($result && $db->num_rows($result)){
      $user = $db->fetch_assoc($result);
      $password_request = sha1($password);
      if($password_request === $user['password'] ){
        return $user['id'];
      }
    }
   return false;
  }
  /*--------------------------------------------------------------*/
  /* Login with the data provided in $_POST,
  /* coming from the login_v2.php form.
  /* If you used this method then remove authenticate function.
 /*--------------------------------------------------------------*/
   function authenticate_v2($username='', $password='') {
     global $db;
     $username = $db->escape($username);
     $password = $db->escape($password);
     $sql  = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
     $result = $db->query($sql);
     if($result && $db->num_rows($result)){
       $user = $db->fetch_assoc($result);
       $password_request = sha1($password);
       if($password_request === $user['password'] ){
         return $user;
       }
     }
    return false;
   }


  /*--------------------------------------------------------------*/
  /* Find current log in user by session id
  /*--------------------------------------------------------------*/
  function current_user(){
      static $current_user;
      global $db;
      if(!$current_user){
         if(isset($_SESSION['user_id'])):
             $user_id = intval($_SESSION['user_id']);
             $current_user = find_by_id('users',$user_id);
        endif;
      }
    return $current_user;
  }
  /*--------------------------------------------------------------*/
  /* Find all user by
  /* Joining users table and user gropus table
  /*--------------------------------------------------------------*/
  function find_all_user(){
      global $db;
      $results = array();
      $sql = "SELECT u.id,u.name,u.username,u.user_level,u.status,u.last_login,";
      $sql .="g.group_name ";
      $sql .="FROM users u ";
      $sql .="LEFT JOIN user_groups g ";
      $sql .="ON g.group_level=u.user_level ORDER BY u.name ASC";
      $result = find_by_sql($sql);
      return $result;
  }
  /*--------------------------------------------------------------*/
  /* Function to update the last log in of a user
  /*--------------------------------------------------------------*/

 function updateLastLogIn($user_id)
    {
        global $db;
    $date = make_date();
    $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";
    $result = $db->query($sql);
    return ($result && $db->affected_rows() === 1 ? true : false);
    }

  /*--------------------------------------------------------------*/
  /* Find all Group name
  /*--------------------------------------------------------------*/
  function find_by_groupName($val)
  {
    global $db;
    $sql = "SELECT group_name FROM user_groups WHERE group_name = '{$db->escape($val)}' LIMIT 1 ";
    $result = $db->query($sql);
    return($db->num_rows($result) === 0 ? true : false);
  }
  /*--------------------------------------------------------------*/
  /* Find group level
  /*--------------------------------------------------------------*/
  function find_by_groupLevel($level)
  {
    global $db;
    $sql = "SELECT group_level FROM user_groups WHERE group_level = '{$db->escape($level)}' LIMIT 1 ";
    $result = $db->query($sql);
    return($db->num_rows($result) === 0 ? true : false);
  }
  /*--------------------------------------------------------------*/
  /* Function for cheaking which user level has access to page
  /*--------------------------------------------------------------*/
   function page_require_level($require_level){
     global $session;
     $current_user = current_user();
     $login_level = find_by_groupLevel($current_user['user_level']);
     //if user not login
     if (!$session->isUserLoggedIn()):
            $session->msg('d','Please login...');
            redirect('index.php', false);
      //if Group status Deactive
     elseif(['group_status'] === '0'):
           $session->msg('d','This level user has been band!');
           redirect('home.php',false);
      //cheackin log in User level and Require level is Less than or equal to
     elseif($current_user['user_level'] <= (int)$require_level):
              return true;
      else:
            $session->msg("d", "Sorry! you dont have permission to view the page.");
            redirect('home.php', false);
        endif;

     }
     
   /*--------------------------------------------------------------*/
   /* Function for Finding all product name
   /* JOIN with categorie  and media database table
   /*--------------------------------------------------------------*/
  function join_product_table(){
     global $db;
     $sql  =" SELECT p.id,p.name,p.quantity,p.product_img,p.date,c.name";
    $sql  .=" AS categorie,m.product_img AS image";
    $sql  .=" FROM products p";
    $sql  .=" LEFT JOIN categories c ON c.id = p.categorie_id";
    $sql  .=" LEFT JOIN media m ON m.id = p.product_img";
    $sql  .=" ORDER BY p.id ASC";
    return find_by_sql($sql);

   }
  /*--------------------------------------------------------------*/
  /* Function for Finding all product name
  /* Request coming from ajax.php for auto suggest
  /*--------------------------------------------------------------*/

   function find_product_by_title($product_name){
     global $db;
     $p_name = remove_junk($db->escape($product_name));
     $sql = "SELECT name FROM products WHERE name like '%$p_name%' LIMIT 5";
     $result = find_by_sql($sql);
     return $result;
   }

  /*--------------------------------------------------------------*/
  /* Function for Finding all product info by product title
  /* Request coming from ajax.php
  /*--------------------------------------------------------------*/
  function find_all_product_info_by_title($title){
    global $db;
    $sql  = "SELECT * FROM products ";
    $sql .= " WHERE name ='{$title}'";
    $sql .=" LIMIT 1";
    return find_by_sql($sql);
  }

  /*--------------------------------------------------------------*/
  /* Function for Update product quantity
  /*--------------------------------------------------------------*/
  function update_product_qty($qty,$p_id){
    global $db;
    $qty = (int) $qty;
    $id  = (int)$p_id;
    $sql = "UPDATE products SET quantity=quantity -'{$qty}' WHERE id = '{$id}'";
    $result = $db->query($sql);
    return($db->affected_rows() === 1 ? true : false);

  }
  /*--------------------------------------------------------------*/
  /* Function for Display Recent product Added
  /*--------------------------------------------------------------*/
function Damaged_Equipments(): string {
  // Function logic here
  
  $result = ''; // Assign a value to $result

  // Return the result
  return $result;
}
function find_products_by_category($category_id) {
  global $db;

  $category_id = (int)$category_id;

  $sql = "SELECT * FROM products WHERE categorie_id = {$category_id}";
  $result = find_by_sql($sql);

  return $result;
}


// Function to insert data into the database
function insert($table, $data)
{
    global $db;
    $columns = implode(", ", array_keys($data));
    $values = "'" . implode("', '", array_values($data)) . "'";
    $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
    $result = $db->query($sql);
    return $result;
}
function find_product_by_name($product_name) {
  global $db; // Assuming $db is your database connection object

  // Sanitize the product name to prevent SQL injection
  $safe_product_name = $db->escape($product_name);

  // Construct the SQL query to find the product by its name
  $sql = "SELECT * FROM products WHERE name = '{$safe_product_name}' LIMIT 1";

  // Execute the query
  $result = $db->query($sql);

  // Check if the query was successful and if any rows were returned
  if ($result && $db->num_rows($result) > 0) {
      // Fetch the product details as an associative array
      $product = $db->fetch_assoc($result);
      return $product;
  } else {
      // Product not found
      return false;
  }
}

?>
