<?php
require_once("interface.php");

class RegisterInterface extends Interface_class {
    private function __construct() {}//static only
    
    public static function register($uname, $pass){
        $uname = self::prepVar($uname);
        $pass = self::prepVar($pass);
        self::$db->querySingle("insert into playerinfo (uname, pass, zone, posx, posy, audioURL, peerid, health, kills) values ($uname, $pass,0,0,0,'aurl','123qwe',3,0)");
    }
}
?>