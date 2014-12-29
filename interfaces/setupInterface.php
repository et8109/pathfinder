<?php
require_once("interface.php");

class SetupInterface extends Interface_class{
    private function __construct() {}//static only
    
    public static function getPlayerInfo($pid){
        $pid = self::prepVar($pid);
        $r = self::$db->querySingle("select posx, posy, peerid, audioURL from playerinfo where id=$pid");
        return $r;
    }
    
    public static function getSpriteAudio(){
        $audioId = self::prepVar("s0");
        $r = self::$db->queryMulti("select url from audio where objid=$audioId");
        return $r;
    }
}
?>