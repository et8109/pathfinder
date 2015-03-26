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
        "lastAudio int(3),".
        "finish int(10),".
        "start int(10),".
        "PRIMARY KEY (id)".
        ")");
    }

    public static function init(){
        self::$db->querySingle(
            "INSERT INTO enemies (id, type, zonex, zoney, health, lastAudio, finish, start) 
                           values (0,    0,     0,    1,       4,         1,      0,     0)");

    }

    public static function getInZone($zonex, $zoney){
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $r = self::$db->queryMulti("select id, type, zonex, zoney, finish, start, lastAudio, health from enemies where zonex=$zonex and zoney=$zoney");
        return $r;
    }

    public static function getZonePrep($zonex, $zoney){
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $r = self::$db->queryMulti("select id, type from enemies where zonex=$zonex and zoney=$zoney");
        for($i=0; isset($r[$i]); $i++){
            $objid = "e".$r[$i]['type'];
            $r[$i]['audioURLs'] = Audio::getURLs($objid);
        }
        return $r;
    }

    public static function resetEnemy($zonex,$zoney,$health,$enemyId) {
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $enemyId = self::prepVar($enemyId);
        self::$db->querySingle("update enemies set zonex=$zonex, zoney=$zoney ,health=$health where id=$enemyId");
        return;
    }

    public static function lowerEnemyHealth($eid, $zonex, $zoney) {
        $eid = self::prepVar($eid);
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        self::$db->querySingle("update enemies set health=health-1 where health>1 and id=$eid and zonex=$zonex and zoney=$zoney");
        return self::$db->lastQueryNumRows() != 1; //returns true if dead
    }

    public static function addEvent($startTime, $endTime, $audioInt, $enemyId) {
        $startTime = self::prepVar($startTime);
        $endTime = self::prepVar($endTime);
        $audioInt = self::prepVar($audioInt);
        $enemyId = self::prepVar($enemyId);
        self::$db->querySingle("update enemies set start=$startTime, finish=$endTime, lastAudio=$audioInt where id=$enemyId");
    }

}
?>
