<?php
require_once("interface.php");

public class MainInterface extends Interface{
    private function __construct() {}//static only
    
    public static function updatePlayerInfo($posx,$posy, $zone, $playerID) throws dbException{
        $posx = $this->prepVar($posx);
        $posy = $this->prepVar($posy);
        $zone = $this->prepVar($zone);
        $playerID = $this->prepVar($playerID);
        Database::querySingle("UPDATE playerinfo SET posx=$posx,posy=$posy,zone=$zone WHERE id=$playerID");
        return;
    }
    
    public static function getPlayerInfo($name) throws dbException{
        $name = $this->prepVar($name);
        $r = Database::querySingle("select zone, health from playerinfo where id=$name");
        return $r;
    }
    
    public static function removeOldPlayerEvents($time) throws dbException{
        $time = $this->prepVar($time);
        query("delete from playerevents where finish < $time");
        return;
    }
    
    public static function getNpcsInZone($zone) throws dbException{
        $zone = $this->prepVar($zone);
        $r = Database::queryMulti("select id,posx,posy,finish,start,lastAudio from npcs where zone=$zone");
        return $r;
    }
    
    public static function getEnemiesInZone($zone) throws dbException{
        $zone = $this->prepVar($zone);
        $enemyResult = Database::queryMulti("select id,posx,posy,finish,start,lastAudio,health from enemies where zone=$zone");
        return $r;
    }
    
    public static function resetEnemy($posX,$posY,$health,$enemyId) throws dbException{
        $posX = $this->prepVar($posX);
        $posY = $this->prepVar($posY);
        $enemyId = $this->prepVar($enemyId);
        Database::querySingle("update enemies set posx=$posX,posy=$posY,health=$health where id=$enemyId");
        return;
    }
    
    public static function getPlayerEventsInZone($zone,$time) throws dbException{
        $zone = $this->prepVar($zone);
        $time = $this->prepVar($time);
        $r = Database::queryMulti("select id,audiotype from playerevents where zone=$zone and start>=$time");
        return $r;
    }
        
    public static function lowerEnemyHealth($eid, $posx, $posy) throws dbException{
        $eid = $this->prepVar($eid);
        $posx = $this->prepVar($posx);
        $posy = $this->prepVar($posy);
        Database::querySingle("update enemies set health=health-1 where health>1 and id=$eid and posx=$posx and posy=$posy");
        return Database::lastQueryNumRows() != 1; //returns true if dead
    }
    
    public static function increasePlayerKills($pid) throws dbException{
        $pid = $this->prepVar($pid);
        Database::querySingle("update playerinfo set kills = kills+1 where playerID=$pid and kills<99");
    }
    
    public static function lowerPlayerHealth($pid) throws dbException{
        $pid = $this->prepVar($pid);
        Database::querySingle("update playerinfo set health=health-1 where id=$pid");
    }
    
    public static function resetPlayer($pid, $health, $posx, $posy) throws dbException{
        $pid = $this->prepVar($pid);
        Database::querySingle("update playerinfo set health=$health, posx=$posx, posy=$posy where id=$pid");
    }
    
    public static function addNPCEvent($startTime, $endTime, $audioInt, $npcid) throws dbException{
        $startTime = $this->prepVar($startTime);
        $endTime = $this->prepVar($endTime);
        $audioInt = $this->prepVar($audioInt);
        $npcid = $this->prepVar($npcid);
        Database::querySingle("update npcs set start=$startTime, finish=$endTime, lastAudio=$audioInt where id=$npcid");
    }
    
    public static function addEnemyEvent($startTime, $endTime, $audioInt, $enemyId) throws dbException{
        $startTime = $this->prepVar($startTime);
        $endTime = $this->prepVar($endTime);
        $audioInt = $this->prepVar($audioInt);
        $enemyId = $this->prepVar($enemyId);
        Database::querySingle("update enemies set start=$startTime, finish=$endTime, lastAudio=$autioInt where id=$enemyId");
    }
    
    public static function addPlayerEvent($startTime, $endTime, $audioInt, $playerid, $zone, $override) throws dbException{
        $startTime = $this->prepVar($startTime);
        $endTime = $this->prepVar($endTime);
        $audioInt = $this->prepVar($audioInt);
        $playerid = $this->prepVar($playerid);
        $zone = $this->prepVar($zone);
        $override = $this->prepVar($override);
        $checkRow = Database::querySingle("select 1 from playerevents where id=$pid limit 1");
        if (Database::lastQueryNumRows() == 1 && !$override) {
            Database::querySingle("insert into playerevents (id,zone,audiotype,start,finish) values ($pid,$zone,$audioInt,$time,$endTime)");
            return true;
        }
        return false;//return if the action is being done
    }
}

?>