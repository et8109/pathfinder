<?php
require_once("interface.php");

public class SetupInterface extends Interface{
    private function __construct() {}//static only
    
    public static function getPlayerInfo($pid) throws dbException{
        $pid = $this->prepVar($pid);
        $r = Database::querySingle("select posx, posy, peerid, audioURL from playerinfo where id=$pid");
        return $r;
    }
}
?>