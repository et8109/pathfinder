<?php

require_once("../inc/database.php");

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
    /**
     *general header, not for game page
     */
    public static function addHeader(){
        include("../inc/header.inc");
    }
    /**
     *Header for the main game page
     */
    public static function addHeaderIndex(){
        include("../inc/headerIndex.inc");
    }
    
    public static function addFooter(){
        include("../inc/footer.inc");
    }
}
//initialize db object
Interface_class::init();
?>