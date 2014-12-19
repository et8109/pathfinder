<?php
require_once("interface.php");

class LogoutInterface extends Interface_class{
    private function __construct() {}//static only
    
    public static function logout($pid){
        $pid = self::prepVar($pid);
        self::$db->querySingle("UPDATE playerinfo SET zone=0 WHERE id=$pid");
    }
}
?>