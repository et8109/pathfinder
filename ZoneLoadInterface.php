<?php
require_once("interface.php");

public class ZoneLoadInterface extends Interface{
    private function __construct() {}//static only
    
    public static function updatePlayerInfo($posx,$posy, $zone, $playerID) throws dbException{
        $posx = $this->prepVar($posx);
        $posy = $this->prepVar($posy);
        $zone = $this->prepVar($zone);
        $playerID = $this->prepVar($playerID);
        Database::querySingle("UPDATE playerinfo SET posx=$posx,posy=$posy,zone=$zone WHERE id=$playerID");
        return;
    }
    
    public static function getAmbientSounds($zone) throws dbException{
        $zone = $this->prepVar($zone);
        $r = Database::queryMulti("select posx,posy,audioURL from ambient where zone=$zone");
        return $r;
    }
    
    public static function getMovementSound($zone) throws dbException{
        $zone = $this->prepVar($zone);
        $r = Database::querySingle("select audioURL from movement where zone=$zone");
        return $r;
    }
    
    public static function getEnemies($zone) throws dbException{
        $zone = $this->prepVar($zone);
        $r = Database::queryMulti("select E.id,E.posx,E.posy,I.audioURL from enemies E, enemyinfo I where zone=$zone and E.id = I.id");
        return $r;
    }
    
    public static function getNpcs($zone) throws dbException{
        $zone = $this->prepVar($zone);
        $r = Database::queryMulti("select id,posx,posy,audioURL from npcs where zone=$zone");
        return $r;
    }
    
    public static function getPlayers($zone, $pId, $numZonesSrt) throws dbException{
        $zone = $this->prepVar($zone);
        $pId = $this->prepVar($pId);
        $numZonesSrt = $this->prepVar($numZonesSrt);
        $r = Database::queryMulti("select peerid from playerinfo where id!=$pId and zone in (".($zone-1-$numZonesSrt).",".($zone-1).",".($zone-1+$numZonesSrt).",".($zone-$numZonesSrt).",".$zone.",".($zone+$numZonesSrt).",".($zone+1+$numZonesSrt).",".($zone+1).",".($zone+1-$numZonesSrt).") and zone != 0");
        return $r;
    }
}
?>