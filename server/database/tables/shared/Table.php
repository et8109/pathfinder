<?php

/**
 * Abstract class for all tables
 */
require_once("../database/database.php");
abstract class Table{
    protected static $db;
    private static $initialized = false;
    abstract public function create();
    abstract public function init();

    protected static function prepVar(){
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
        self::$db = new Database();
        self::$initialized = true;
    }
}
Table::setDb();
?>
