<?php
require_once("shared/Table.php");
/**
 * Stores the ambients sounds for each zone
 */
class Ambient extends Table{
    private function __construct() {}//static only

    public static function create(){
        self::$db->querySingle(
            "CREATE TABLE ambient (".
            "id int(3),".
            "zonex int(3),".
            "zoney int(3),".
            "audioURL varchar(10),".
            "PRIMARY KEY (zonex, zoney, id)".
            ")"
        );
    }

    public static function init(){
        self::$db->querySingle(
            "INSERT INTO ambient (id, zonex, zoney, audioURL) 
                          values (0 ,     1,     1, 'Birds.mp3')");
    }

    public static function getZoneSounds($zone){
        $zone = self::prepVar($zone);
        $r = self::$db->queryMulti("select zonex,zoney,id from ambient where zone=$zone");
        for($i=0; isset($r[$i]); $i++){
            $objid = "a".$r[$i]['id'];
            $r[$i]['audioURL'] = self::getURLs($objid);
        }
        return $r;
    }
}
?>
