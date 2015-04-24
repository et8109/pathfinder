<?php
require_once("shared/Table.php");
/**
 * The individual enemies, current stats, and type
 */
class Enemies extends Table{
    private function __construct() {}//static only

    public static function create(){
        self::$db->querySingle("CREATE TABLE enemies (".
        "id int(3),".
        "type int(3),".
        "zonex int(3),".
        "zoney int(3),".
        "health int(3),".
        "PRIMARY KEY (id)".
        ")");
    }

    public static function init(){
        self::$db->querySingle(
            "INSERT INTO enemies (id, type, zonex, zoney, health) 
                           values (0,    0,     0,    1,       4)");

    }

    public static function getInZone($zonex, $zoney){
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $r = self::$db->queryMulti("select id, type, zonex, zoney, health from enemies where zonex=$zonex and zoney=$zoney");
        foreach($r as &$e){
            $e['audios'] = Audio::getUrls('e'.$e['id']);
        }
        return $r;
    }

    public static function resetEnemy($zonex,$zoney,$health,$enemyId) {
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $enemyId = self::prepVar($enemyId);
        self::$db->querySingle("update enemies set zonex=$zonex, zoney=$zoney ,health=$health where id=$enemyId");
    }

    public static function reposition($zonex, $zoney, $enemyId){
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $enemyId = self::prepVar($enemyId);
        self::$db->querySingle("update enemies set zonex=$zonex, zoney=$zoney where id=$enemyId");

    }

    /**
     * returns true if dead
     */
    public static function lowerHealth($eid, $zonex, $zoney) {
        $eid = self::prepVar($eid);
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        self::$db->querySingle("update enemies set health=health-1 where health>1 and id=$eid and zonex=$zonex and zoney=$zoney");
        return self::$db->lastQueryNumRows() != 1; //returns true if dead
    }

    /*public static function addEvent($startTime, $endTime, $audioInt, $enemyId) {
        $startTime = self::prepVar($startTime);
        $endTime = self::prepVar($endTime);
        $audioInt = self::prepVar($audioInt);
        $enemyId = self::prepVar($enemyId);
        self::$db->querySingle("update enemies set start=$startTime, finish=$endTime, lastAudio=$audioInt where id=$enemyId");
    }*/

}
?>
