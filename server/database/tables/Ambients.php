<?php
require_once("shared/Table.php");
/**
 * Stores the ambients sounds for each zone
 */
class Ambients extends Table{
    private function __construct() {}//static only

    public static function create(){
        self::$db->querySingle(
            "CREATE TABLE ambients (".
            "id int(3),".
            "zonex int(3),".
            "zoney int(3),".
            "PRIMARY KEY (zonex, zoney, id)".
            ")"
        );
    }

    public static function init(){
        self::$db->querySingle(
            "INSERT INTO ambients (id, zonex, zoney) 
                          values (0 ,     1,     1)");
    }

    public static function getZonePrep($zonex, $zoney){
        $ids = self::getInZone($zonex, $zoney);
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        foreach($ids as &$id){//might not work becuase foreach doe snot give actual link to array
            $objid = "a".$id['id'];
            $id['audioURLs'] = Audio::getURLs($objid);
        }
        return $ids;
    }

    /**
     * returns the ids of the audio in the given zone
     */
    public static function getInZone($zonex, $zoney, $getUrls){
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        if($getUrls){
            return collapseUrls(self::$db->queryMulti("select A.id, U.url from ambients A, audio U where A.zonex=$zonex and A.zoney=$zoney and U.objid=a+A.id"));
        } else{
            return self::$db->queryMulti("select id, from ambients where zonex=$zonex and zoney=$zoney");
        }
    }
}
?>
