<?php
session_start();

// Function to find a record by ID in the database
function find_by_id_id ($table, $id) {
    global $db;
    $id = (int)$id;
    $result_set = $db->query("SELECT * FROM {$table} WHERE id={$id} LIMIT 1");
    return $db->fetch_array($result_set);
}

class Session {
    public $msg;
    private $user_is_logged_in = false;

    function __construct(){
        $this->flash_msg();
        $this->userLoginSetup();
    }

    public function isUserLoggedIn(){
        return $this->user_is_logged_in;
    }

    public function login($user_id){
        $_SESSION['user_id'] = $user_id;
    }

    private function userLoginSetup()
    {
        if(isset($_SESSION['user_id']))
        {
            $this->user_is_logged_in = true;
        } else {
            $this->user_is_logged_in = false;
        }
    }

    public function logout(){
        unset($_SESSION['user_id']);
    }

    public function msg($type ='', $msg =''){
        if(!empty($msg)){
            if(strlen(trim($type)) == 1){
                $type = str_replace( array('d', 'i', 'w','s'), array('danger', 'info', 'warning','success'), $type );
            }
            $_SESSION['msg'][$type] = $msg;
        } else {
            return $this->msg;
        }
    }

    private function flash_msg(){
        if(isset($_SESSION['msg'])) {
            $this->msg = $_SESSION['msg'];
            unset($_SESSION['msg']);
        } else {
            $this->msg;
        }
    }
}

$session = new Session();
$msg = $session->msg();

?>
