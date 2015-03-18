<?php
require_once("shared/Table.php");
/**
 * Stores the ambients sounds for each zone
 */
class Ambients extends Table{
    private function __construct() {}//static only

    public static function create(){
        self::$db->querySingle(
            "CREATE TABLE ambient (".
            "id int(3),".
            "zonex int(3),".
            "zoney int(3),".
            "PRIMARY KEY (zonex, zoney, id)".
            ")"
        );
    }

    public static function init(){
        self::$db->querySingle(
            "INSERT INTO ambient (id, zonex, zoney, audioURL) 
                          values (0 ,     1,     1, 'Birds.mp3')");
    }

    public static function getZonePrep($zonex, $zoney){
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $r = self::$db->queryMulti("select id from ambient where zonex=$zonex and zoney=$zoney");
        for($i=0; isset($r[$i]); $i++){
            $objid = "a".$r[$i]['id'];
            $r[$i]['audioURLs'] = Audio::getURLs($objid);
        }
        return $r;
    }
}
?>
