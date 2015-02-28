<?php
class Database {
    private static $hostName = "localhost";
    private static $username = "root";//"ignatymc_admin";
    private static $password = null;//"1Gn4tym";
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
        $con = mysqli_connect(self::$hostName,self::$username,self::$password,self::$name);
        //check connection
        if (mysqli_connect_errno()){
            throw new dbException("could not connect to database", dbException::CODE_COULD_NOT_CONNECT);
        }
        return $con;
    }
}

class dbException extends Exception{
    const CODE_COULD_NOT_CONNECT = 0;
    private $msg;
    
    function __construct($msg, $code){
        parent::__construct($msg,$code);
    }
}


?>