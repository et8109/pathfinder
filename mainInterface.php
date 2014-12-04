<?php
require_once("interface.php");

public class MainInterface extends Interface{
    private function __construct() {}//static only
    
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
    
    
}

?>