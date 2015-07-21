<?php

/**
 * Abstract class for all tables
 */
require_once(constants::server_root."/database/core/database.php");
abstract class Table{
    protected static $db;
    private static $initialized = false;

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

    /*protected static function collapseUrls($arr){
        //check if only 1 url
        if(count($arr) == 1){
            $arr = $arr[0];
            $arr['urls'] = array($arr['url']);
            unset($arr['url']);
            return $arr;
        }
        $list = [];
        $num = count($arr);
        for($i=0; $i<$num;){
            $row = $arr[$i];
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
    }*/
}
Table::setDb();
?>
