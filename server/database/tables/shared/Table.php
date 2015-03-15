<?php

/**
 * Abstract class for all tables
 */
require_once(constants::server_root."/database/core/database.php");
abstract class Table{
    protected static $db;
    private static $initialized = false;
    abstract static public function create();
    abstract static public function init();

    protected static function prepVar($var){
        self::$db->escapeString($var);
        //if not a number, surround in quotes
        if(!is_numeric($var)){
            $var = "'".$var."'";
        }
        return $var;
    }

    public static function setDb(){
        if(self::$initialized){
            throw new Exception("Cannot initialize db twice");
        }
        self::$db = new DBCore();
        self::$initialized = true;
    }
}
Table::setDb();
?>
