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

    public static function getInZone($zonex, $zoney, $getUrls){
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        if($getUrls){
            return self::collapseUrls(self::$db->queryMulti("select E.id, E.type, E.zonex, E.zoney, E.health, U.url from enemies E, audio U where zonex=$zonex and zoney=$zoney and U.objid='e+E.id'"));
        }
        $r = self::$db->queryMulti("select id, type, zonex, zoney, health from enemies where zonex=$zonex and zoney=$zoney");
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
