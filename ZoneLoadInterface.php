<?php
require_once("interface.php");

class ZoneLoadInterface extends Interface_class{
    private function __construct() {}//static only
    
    public static function getAmbientSounds($zone) {
        $zone = self::prepVar($zone);
        $r = self::$db->queryMulti("select posx,posy,audioURL from ambient where zone=$zone");
        return $r;
    }
    
    public static function getMovementSound($zone) {
        $zone = self::prepVar($zone);
        $r = self::$db->querySingle("select audioURL from movement where zone=$zone");
        return $r;
    }
    
    public static function getEnemies($zone) {
        $zone = self::prepVar($zone);
        $r = self::$db->queryMulti("select E.id,E.posx,E.posy,I.audioURL from enemies E, enemyinfo I where zone=$zone and E.id = I.id");
        return $r;
    }
    
    public static function getNpcs($zone) {
        $zone = self::prepVar($zone);
        $r = self::$db->queryMulti("select id,posx,posy,audioURL from npcs where zone=$zone");
        return $r;
    }
    
    public static function getPlayers($zone, $pId, $numZonesSrt) {
        $zone = self::prepVar($zone);
        $pId = self::prepVar($pId);
        $numZonesSrt = self::prepVar($numZonesSrt);
        $r = self::$db->queryMulti("select peerid from playerinfo where id!=$pId and zone in (".($zone-1-$numZonesSrt).",".($zone-1).",".($zone-1+$numZonesSrt).",".($zone-$numZonesSrt).",".$zone.",".($zone+$numZonesSrt).",".($zone+1+$numZonesSrt).",".($zone+1).",".($zone+1-$numZonesSrt).") and zone != 0");
        return $r;
    }
}
?>