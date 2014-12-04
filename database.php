<?php
public class Database {
    private static hostName = "localhost";
    private static username = "ignatymc_admin";
    private static password = "1Gn4tym";
    private static name = "ignatymc_audioGame";
    private $con = getConnection();
    private function __construct() {}//static only
    
    public static function _query($sql){
        $result = mysqli_query($this->con, $sql);
        return $result;
    }
    
    public static function querySingle($sql){
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
    
    public static function queryMulti($sql){
        $result = mysqli_query($this->con, $sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public static function lastQueryNumRows(){
        return mysqli_affected_rows($this->con);
    }
    
    private static function getConnection(){
        $con = mysqli_connect(Database::hostName,Database::username,Database::password,Database::name);
        //check connection
        if (mysqli_connect_errno()){
            throw new dbException("could not connect to database", dbException::LVL_FATAL);
        }
        return $con;
    }
}

public class dbException {
    const LVL_WARNING = 0;
    const LVL_FATAL = 1;
    private $msg;
    private $level;
    
    function __construct($msg, $level){
        this->$msg = $msg;
        this->$level = $level;
    }
}


?>