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

    /**
     * returns the ids of the audio in the given zone
     */
    public static function getInZone($zonex, $zoney){
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $r = self::$db->queryMulti("select id from ambients where zonex=$zonex and zoney=$zoney");
        foreach($r as &$n){
            $n['audios'] = Audio::getUrls('a'.$n['id']);
        }
        return $r;

    }
}
?>
