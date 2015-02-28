<?php
require_once("interface.php");

class ZoneLoadInterface extends Interface_class{
    private function __construct() {}//static only
    
    private static function getURLs($objid){
        $objid = self::prepVar($objid);
        $r = self::$db->queryMulti("select url from audio where objid=$objid");
        $urls = "";
        $i=0;
        $len=count($r);
        foreach($r as $url){
            $urls .= $url['url'];
            $i++;
            if($i < $len){
                $urls .= ",";
            }
        }
        return $urls;
    }
    
    public static function getAmbientSounds($zone) {
        $zone = self::prepVar($zone);
        $r = self::$db->queryMulti("select posx,posy,id from ambient where zone=$zone");
        for($i=0; isset($r[$i]); $i++){
            $objid = "a".$r[$i]['id'];
            $r[$i]['audioURL'] = self::getURLs($objid);
        }
        return $r;
    }
    
    public static function getMovementSound($zone) {
        $r = array();
        $objid = "m$zone";
        $r['audioURL'] = self::getURLs($objid);
        return $r;
    }
    
    public static function getEnemies($zone) {
        $zone = self::prepVar($zone);
        $r = self::$db->queryMulti("select id,type,posx,posy from enemies where zone=$zone");
        for($i=0; isset($r[$i]); $i++){
            $objid = "e".$r[$i]['type'];
            $r[$i]['audioURL'] = self::getURLs($objid);
        }
        return $r;
    }
    
    public static function getNpcs($zone) {
        $zone = self::prepVar($zone);
        $r = self::$db->queryMulti("select id,posx,posy,audioURL from npcs where zone=$zone");
        for($i=0; isset($r[$i]); $i++){
            $objid = "n".$r[$i]['id'];
            $r[$i]['audioURL'] = self::getURLs($objid);
        }
        return $r;
    }
    
    public static function getPlayers($zone, $pId, $numZonesSrt) {
        $zone = self::prepVar($zone);
        $pId = self::prepVar($pId);
        $numZonesSrt = self::prepVar($numZonesSrt);
        $r = self::$db->queryMulti("select id, peerid from playerinfo where id!=$pId and zone in (".($zone-1-$numZonesSrt).",".($zone-1).",".($zone-1+$numZonesSrt).",".($zone-$numZonesSrt).",".$zone.",".($zone+$numZonesSrt).",".($zone+1+$numZonesSrt).",".($zone+1).",".($zone+1-$numZonesSrt).") and zone != 0");
        for($i=0; isset($r[$i]); $i++){
            $objid = "p".$r[$i]['id'];
            $r[$i]['audioURL'] = self::getURLs($objid);
        }
        return $r;
    }
}
?>