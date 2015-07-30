<?php
require_once("shared/Table.php");
/**
 * The individual players, thier stats, and other info
 */
class PlayerInfo extends Table{
    private function __construct() {}//static only

    public static function create(){
        self::$db->querySingle("CREATE TABLE playerinfo (".
            "id int(3) AUTO_INCREMENT,".
            "uname varchar(10),".
            "pass varchar(20),".
            "zonex int(3),".
            "zoney int(3),".
            "peerid varchar(10),".
            "health int(3),".
            "kills int(3),".
            "PRIMARY KEY (id)".
            ")");
    }

    public static function init(){
        self::$db->querySingle(
            "INSERT INTO playerinfo (id,   uname,    pass, zonex, zoney,     peerid, health, kills) 
                             values ( 1, 'guest', 'guest',     0,   0, 'abcd1234',      3,     0)");

    }

    public static function logout($pid){
        $pid = self::prepVar($pid);
        self::$db->querySingle("UPDATE playerinfo SET zone=0 WHERE id=$pid");
    }

    /**
     * returns true if the user exists
     * false if not found
     */
    public static function getInfoLogin($uname, $pass){
        $uname = self::prepVar($uname);
        $pass = self::prepVar($pass);
        $r = self::$db->querySingle("select id from playerinfo where uname=$uname and pass=$pass");
        return $r;
    }   

    public static function getInfoById($pid){
        $pid = self::prepVar($pid);
        $r = self::$db->querySingle("select id, zonex, zoney, peerid, health from playerinfo where id=$pid");
        $r['audios'] = Audio::getUrls('p'.$r['id']);
        return $r;
    }

    public static function getInfoByName($name){
        $name = self::prepVar($name);
        $r = self::$db->querySingle("select zonex, zoney, health from playerinfo where uname=$name");
        return $r;
 
    }

    public static function getInZone($zonex, $zone, $getUrls){
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $r = self::$db->queryMulti("select id, zonex, zoney, health from playerinfo where zonex=$zonex and zoney=$zoney");
        foreach($r as &$n){
            $n['audios'] = Audio::getUrls('p'.$n['id']);
        }
        return $r; 
    }

    public static function register($uname, $pass){
        $uname = self::prepVar($uname);
        $pass = self::prepVar($pass);
        self::$db->querySingle("insert into playerinfo ( uname,  pass, zonex, zoney,  peerid, health, kills) 
                                                values ($uname, $pass,     0,   0, '123qwe',      3,     0)");
    }

    /*public static function getZonePlayers($zonex, $zoney, $pid, $numZonesSrt){
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $pid = self::prepVar($pid);
        $numZonesSrt = self::prepVar($numZonesSrt);
        $r = self::$db->queryMulti("select id, peerid from playerinfo where id!=$pid and zone in (".($zone-1-$numZonesSrt).",".($zone-1).",".($zone-1+$numZonesSrt).",".($zone-$numZonesSrt).",".$zone.",".($zone+$numZonesSrt).",".($zone+1+$numZonesSrt).",".($zone+1).",".($zone+1-$numZonesSrt).") and zone != 0");
        for($i=0; isset($r[$i]); $i++){
            $objid = "p".$r[$i]['id'];
            $r[$i]['audioURL'] = self::getURLs($objid);
        }
        return $r;
    }*/
    public static function updateInfo($pid, $zonex, $zoney) {
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $pid = self::prepVar($pid);
        self::$db->querySingle("UPDATE playerinfo SET zonex=$zonex,zoney=$zoney WHERE id=$pid");
        return;
    }

    public static function resetPlayer($pid, $health, $zonex, $zoney) {
        $zonex = self::prepVar($zonex);
        $zoney = self::prepVar($zoney);
        $health = self::prepVar($health);
        $pid = self::prepVar($pid);
        self::$db->querySingle("update playerinfo set health=$health, zonex=$zonex, zoney=$zoney where id=$pid");
    }


    public static function increaseKills($pid) {
        $pid = self::prepVar($pid);
        self::$db->querySingle("update playerinfo set kills = kills+1 where playerID=$pid and kills<99");
    }

    public static function lowerHealth($pid){
        $pid = self::prepVar($pid);
        self::$db->querySingle("update playerinfo set health=health-1 where id=$pid");
    }
  
}
?>