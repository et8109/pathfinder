<?php
require_once("shared/Table.php");
/**
 * Events when players run their own audio 
 */
class PlayerEvents extends Table{
    private function __construct() {}//static only

    public static function create(){
        self::$db->querySingle(
            "CREATE TABLE playerevents (".
            "id int(3),".
            "zonex int(3),".
            "zoney int(3),".
            "audiotype int(3),".
            "finish int(10),".
            "start int(10),".
            "PRIMARY KEY (id)".
            ")");
    }

    public static function init(){
    }

   public static function removeExpired($time) {
        $time = self::prepVar($time);
        self::$db->querySingle("delete from playerevents where finish < $time");
        return;
   }

   public static function getPlayerEventsInZone($zonex, $zoney ,$time) {
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $time = self::prepVar($time);
        $r = self::$db->queryMulti("select id,audiotype from playerevents where zonex=$zonex, zoney=$zoney and start>=$time");
        return $r;
    }

    public static function addEvent($startTime, $endTime, $audioInt, $playerid, $zonex, $zoney, $override) {
        $startTime = self::prepVar($startTime);
        $endTime = self::prepVar($endTime);
        $audioInt = self::prepVar($audioInt);
        $playerid = self::prepVar($playerid);
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $override = self::prepVar($override);
        $checkRow = self::$db->querySingle("select 1 from playerevents where id=$playerid limit 1");
        if (self::$db->lastQueryNumRows() == 1 && !$override) {
            self::$db->querySingle("insert into playerevents (id,zonex, zoney, audiotype,start,finish) values ($pid,$zonex, $zoney, $audioInt,$time,$endTime)");
            return true;
        }
        return false;//return if the action is being done
    }

}
?>
