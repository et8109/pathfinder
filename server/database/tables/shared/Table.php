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

    private static collapseUrls($arr){
        $list = [];
        $num = size($arr);
        for($i=0; $i<$num;;){
            $row = $arr[$i]
            $urls = [];
            //get all urls
            while($i < $num && $arr[$i]['id'] == $row['id']){
                $urls[] = $arr[$i]['url'];
                $i++;
            }
            unset($row['url']);
            $row['urls'] = $urls;
            $list[] = $row;
        }
        return $list;
    }
}
Table::setDb();
?>
