<?php
require_once("shared/Table.php");
/**
 * Audio info
 */
class Audio extends Table{
    private function __construct() {}//static only

    public static function create(){
        self::$db->querySingle(
            "CREATE TABLE audio (".
            "id int(3),".
            "objid varchar(4),".
            "url varchar(10),".
            "length int,".
            "PRIMARY KEY (objid, id)".
            ")");
    }

    public static function init(){
        self::$db->querySingle(
            "INSERT INTO audio (objid, id, url, length) values ".
            "('a0', 0, 'Birds.mp3', 2),".//ambient
            "('e0', 0, 'Growl.mp3', 7),".//enemies
            "('e0', 1, 'Chomp.mp3', 2),".
            "('e0', 2, 'ed.mp3', 2),".
            "('m1', 0, 'carpetStep.wav', 6),".//movement
            "('n0', 0, 'wc.mp3', 3),".//npcs
            "('n0', 1, 'Kntq.mp3', 5),".
            "('n0', 2, 'Knty.mp3', 6),".
            "('n0', 3, 'Kntn.mp3', 5),".
            "('p1', 0, 'attack.mp3', 3),".//player
            "('s0', 0, 'Lowlife.mp3', 4),".//sprite
            "('s0', 1, 'Dead.mp3', 4)"
            );
    }

    /*public static function getSpriteAudio(){
        $audioId = self::prepVar("s0");
        $r = self::$db->queryMulti("select url from audio where objid=$audioId");
        return $r;
    }*/

    /**
     * returns an array of the object's audio urls
     */
    public static function getUrls($key){
        $key = self::prepVar($key);
        $r = self::$db->queryMulti("select url from audio where objid=$key");
        $urls = [];
        $i=0;
        $len=count($r);
        foreach($r as $url){
            $urls[] = $url['url'];
            $i++;
        }
        return $urls;
    }
}
?>
