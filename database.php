<?php
class Database {
    private static $hostName = "localhost";
    private static $username = "ignatymc_admin";
    private static $password = "1Gn4tym";
    private static $name = "ignatymc_pathfinder";
    private $con;
    public function __construct() {
        $this->con = $this->getConnection();
    }
    
    public function escapeString($str){
        return mysqli_real_escape_string($this->con,$str);
    }
    
    public function _query($sql){
        $result = mysqli_query($this->con, $sql);
        return $result;
    }
    
    public function querySingle($sql){
        $result = mysqli_query($this->con, $sql);
        if(is_bool($result)){
            return false;
        }
        $numRows = mysqli_num_rows($result);
        if($numRows > 1){
            throw new dbException("number of rows returned by query > 1", dbException::LVL_FATAL);
        }
        $row = $result->fetch_assoc();
        mysqli_free_result($result);
        return $row;
    }
    
    public function queryMulti($sql){
        $result = mysqli_query($this->con, $sql);
        $arr =  $result->fetch_all(MYSQLI_ASSOC);
        mysqli_free_result($result);
        return $arr;
    }
    
    public function lastQueryNumRows(){
        return mysqli_affected_rows($this->con);
    }
    
    private function getConnection(){
        $con = mysqli_connect($this::$hostName,$this::$username,$this::$password,$this::$name);
        //check connection
        if (mysqli_connect_errno()){
            throw new dbException("could not connect to database", dbException::LVL_FATAL);
        }
        return $con;
    }
}

class dbException {
    const LVL_WARNING = 0;
    const LVL_FATAL = 1;
    private $msg;
    private $level;
    
    function __construct($msg, $level){
        $this->$msg = $msg;
        $this->$level = $level;
    }
}


?>