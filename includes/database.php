<?php
require_once(LIB_PATH_INC.DS."config.php");

class MySqli_DB {

    public $con; // Database connection property
    public $query_id;

    function __construct() {
      $this->db_connect(); // Call the method to establish database connection
    }

    public function db_connect()
    {
      // Establish database connection
      $this->con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

      // Check if connection is successful
      if(!$this->con) {
        die("Database connection failed: " . mysqli_connect_error());
      }
    }

    public function db_disconnect()
    {
      if(isset($this->con))
      {
        mysqli_close($this->con);
        unset($this->con);
      }
    }

    public function query($sql)
    {
      if (trim($sql != "")) {
          $this->query_id = $this->con->query($sql);
          if (!$this->query_id) {
              die("Error on this Query: " . $this->con->error);
          }
          return $this->query_id;
      } else {
          die("Empty query!");
      }
    }

    public function fetch_array($statement)
    {
      return mysqli_fetch_array($statement);
    }

    public function fetch_object($statement)
    {
      return mysqli_fetch_object($statement);
    }

    public function fetch_assoc($statement)
    {
      return mysqli_fetch_assoc($statement);
    }

    public function num_rows($statement)
    {
      return mysqli_num_rows($statement);
    }

    public function insert_id()
    {
      return mysqli_insert_id($this->con);
    }

    public function affected_rows()
    {
      return mysqli_affected_rows($this->con);
    }

    public function escape($str){
       return $this->con->real_escape_string($str);
    }

    public function while_loop($loop){
       $results = array();
       while ($result = $this->fetch_array($loop)) {
          $results[] = $result;
       }
       return $results;
    }

}

$db = new MySqli_DB(); // Initialize database connection
?>