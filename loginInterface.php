<?php
require_once("interface.php");

class LoginInterface extends Interface_class {
    private function __construct() {}//static only
    
    public static function getInfo($uname, $pass){
        $uname = self::prepVar($uname);
        $pass = self::prepVar($pass);
        $r = self::$db->querySingle("select id,peerid,posx,posy,audioURL from playerinfo where uname=$uname and pass=$pass");
        return $r;
    }
}
?>