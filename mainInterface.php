<?php
require_once("interface.php");

class MainInterface extends Interface_class{
    private function __construct() {}//static only
    
    public static function updatePlayerInfo($posx,$posy, $zone, $playerID) {
        $posx = self::prepVar($posx);
        $posy = self::prepVar($posy);
        $zone = self::prepVar($zone);
        $playerID = self::prepVar($playerID);
        self::$db->querySingle("UPDATE playerinfo SET posx=$posx,posy=$posy,zone=$zone WHERE id=$playerID");
        return;
    }
    
    public static function getPlayerInfo($name) {
        $name = self::prepVar($name);
        $r = self::$db->querySingle("select zone, health from playerinfo where id=$name");
        return $r;
    }
    
    public static function removeOldPlayerEvents($time) {
        $time = self::prepVar($time);
        self::$db->querySingle("delete from playerevents where finish < $time");
        return;
    }
    
    public static function getNpcsInZone($zone) {
        $zone = self::prepVar($zone);
        $r = self::$db->queryMulti("select id,posx,posy,finish,start,lastAudio from npcs where zone=$zone");
        return $r;
    }
    
    public static function getEnemiesInZone($zone) {
        $zone = self::prepVar($zone);
        $enemyResult = self::$db->queryMulti("select id,posx,posy,finish,start,lastAudio,health from enemies where zone=$zone");
        return $r;
    }
    
    public static function resetEnemy($posX,$posY,$health,$enemyId) {
        $posX = self::prepVar($posX);
        $posY = self::prepVar($posY);
        $enemyId = self::prepVar($enemyId);
        self::$db->querySingle("update enemies set posx=$posX,posy=$posY,health=$health where id=$enemyId");
        return;
    }
    
    public static function getPlayerEventsInZone($zone,$time) {
        $zone = self::prepVar($zone);
        $time = self::prepVar($time);
        $r = self::$db->queryMulti("select id,audiotype from playerevents where zone=$zone and start>=$time");
        return $r;
    }
        
    public static function lowerEnemyHealth($eid, $posx, $posy) {
        $eid = self::prepVar($eid);
        $posx = self::prepVar($posx);
        $posy = self::prepVar($posy);
        self::$db->querySingle("update enemies set health=health-1 where health>1 and id=$eid and posx=$posx and posy=$posy");
        return self::$db->lastQueryNumRows() != 1; //returns true if dead
    }
    
    public static function increasePlayerKills($pid) {
        $pid = self::prepVar($pid);
        self::$db->querySingle("update playerinfo set kills = kills+1 where playerID=$pid and kills<99");
    }
    
    public static function lowerPlayerHealth($pid) {
        $pid = self::prepVar($pid);
        self::$db->querySingle("update playerinfo set health=health-1 where id=$pid");
    }
    
    public static function resetPlayer($pid, $health, $posx, $posy) {
        $pid = self::prepVar($pid);
        self::$db->querySingle("update playerinfo set health=$health, posx=$posx, posy=$posy where id=$pid");
    }
    
    public static function addNPCEvent($startTime, $endTime, $audioInt, $npcid) {
        $startTime = self::prepVar($startTime);
        $endTime = self::prepVar($endTime);
        $audioInt = self::prepVar($audioInt);
        $npcid = self::prepVar($npcid);
        self::$db->querySingle("update npcs set start=$startTime, finish=$endTime, lastAudio=$audioInt where id=$npcid");
    }
    
    public static function addEnemyEvent($startTime, $endTime, $audioInt, $enemyId) {
        $startTime = self::prepVar($startTime);
        $endTime = self::prepVar($endTime);
        $audioInt = self::prepVar($audioInt);
        $enemyId = self::prepVar($enemyId);
        self::$db->querySingle("update enemies set start=$startTime, finish=$endTime, lastAudio=$autioInt where id=$enemyId");
    }
    
    public static function addPlayerEvent($startTime, $endTime, $audioInt, $playerid, $zone, $override) {
        $startTime = self::prepVar($startTime);
        $endTime = self::prepVar($endTime);
        $audioInt = self::prepVar($audioInt);
        $playerid = self::prepVar($playerid);
        $zone = self::prepVar($zone);
        $override = self::prepVar($override);
        $checkRow = self::$db->querySingle("select 1 from playerevents where id=$pid limit 1");
        if (self::$db->lastQueryNumRows() == 1 && !$override) {
            self::$db->querySingle("insert into playerevents (id,zone,audiotype,start,finish) values ($pid,$zone,$audioInt,$time,$endTime)");
            return true;
        }
        return false;//return if the action is being done
    }
}

?>