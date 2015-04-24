<?php
require_once("shared/Creature.php");

class Player extends Creature{
    const audio_attack = 0;
    const max_health = 4;

    public $sprite;
    public static $type = self::TYPE_PLAYER;

    /**
     * Should only be initialized from inside the class
     */ 
    protected function __construct($id, $audios, Zone $zone, $health){
             parent::__construct($id, $audios, $zone, $health);
        $this->sprite = new Sprite(0, null);
    }

    /**
     * Returns a player class for the given player ID
     */
    public static function fromDatabase($playerID){
        return self::fromDbRow(PlayerInfo::getInfoById($playerID));
    }

    public static function IDfromLogin($uname, $pass){
        $r = PlayerInfo::getInfoLogin($uname, $pass)['id'];
        if($r == null){
            throw new Exception("name/pass combo not found");
        }
        return $r;
    }


    protected static function fromDbRow($row){
        return new Player($row['id'], 
            self::audiosFromDbRow($row),
            new Zone($row['zonex'], $row['zoney']),
            $row['health']);
    }

    /**
     * Walk to a new zone
     */
    public function walk(Zone $zone){
        $this->changeZone($zone);
    }

    /**
     * Setup info required only when initializing the game
     */
    public function getSetupInfo(){
        return PlayerInfo::getInfoById($this->id, true);
    }

    /**
     *updates player in db and sends json for reposition
     */
    public function dead($startTime){
        $this->reposition(0,0);
        PlayerInfo::resetPlayer($this->id,self::max_health,0,0);
        //_addPlayerEvent(1, $time, $zone,true);//death sound as event
        $this->sprite->addAudio(Sprite::audio_dead, $startTime);
    }

    /**
     * Create a new player with default settings
     */
    public static function register($uname, $pass){
        PlayerInfo::register($uname, $pass); 
    }

    /**
     * attacks an enemy
     */
    public function attack($enemy, $startTime){
        return $this->addAudio(self::audio_attack, $startTime);
    }
}

class Sprite extends AudioObject{

    public static $type = self::TYPE_SPRITE;

    /**
     *must be instanciated by player class
     */
    public function __construct($id, $urls){
        parent::__construct($id, $urls);
    }
    const audio_dead = 0;//player is dead
    const audio_lowHealth = 1; //player is at low health
    const audio_edge = 2;//player walks to the edge of the map

    /**
     * returns the sprite audio for walking to the edge of the map
     */
    public function outOfBounds(){
        $this->addAudio(self::audio_edge, 0);
    }
}
?>
