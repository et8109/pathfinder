<?php
require_once("shared/Table.php");
/**
 * THe general enemy info for all enemies of the same type
 */
class EnemyInfo extends Table{
    private function __construct() {}//static only

    public static function create(){
        self::$db->querySingle(
            "CREATE TABLE enemyinfo (".
            "id int(3) AUTO_INCREMENT,".
            "audioURL varchar(30),".
            "PRIMARY KEY (id)".
            ")");
    }

    public static function init(){
        self::$db->querySingle(
            "INSERT INTO enemyInfo (id, audioURL) 
                            values ( 0, 'Growl.mp3,Chomp.mp3,ed.mp3')");
    }
        
}
?>
