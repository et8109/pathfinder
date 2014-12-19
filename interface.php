<?php

require_once("database.php");

/**
 *The interface between the logic/application and the database
 */
class Interface_class {
    protected static $db;
    
    public static function init(){
        Interface_class::$db = new Database();
    }
    
    protected static function prepVar($var){
        $var = Interface_class::$db->escapeString($var);
        //replace ' with ''
        //$var = str_replace("'", "''", $var);
        //if not a number, surround in quotes
        if(!is_numeric($var)){
            $var = "'".$var."'";
        }
        return $var;
    }
}
//initialize db object
Interface_class::init();
?>