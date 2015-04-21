<?php
require_once("shared/Creature.php");

class Player extends Creature{
    const audio_attack = 0;
    const max_health = 4;

    public $sprite;

    /**
     * Should only be initialized from inside the class
     */ 
    protected function __construct($id, $zone, $urls, $health){
             parent::__construct(self::TYPE_PLAYER, 
                $id, $urls, $zone, $health);
        $this->sprite = new Sprite(0, null);
    }

    /**
     * Returns a player class for the given player ID
     */
    public static function fromDatabase($playerID, $getUrls){
        $info = PlayerInfo::getInfoById($playerID, $getUrls);
        return new Player(
            $playerID, 
            new Zone($info["zonex"], $info["zoney"]),
            $info['urls'],
            $info["health"]);
    }

    public static function IDfromLogin($uname, $pass){
        $r = PlayerInfo::getInfoLogin($uname, $pass)['id'];
        if($r == null){
            throw new Exception("name/pass combo not found");
        }
        return $r;
    }

    /**
     * Returns all the players in the given zone
     */
    public static function getInZone($zone, $getUrls){
        $arr = PlayerInfo::getInZone($zone->zonex, $zone->zoney, $getUrls);
        $players = [];
        foreach ($arr as $p){
            $players[] = new Player(
                $p['id'],
                $zone,
                $p['health'],
                $p['urls']
            );
        }
        return $players;
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
    /**
     *must be instanciated by player class
     */
    public function __construct($id, $urls){
        parent::__construct(self::TYPE_SPRITE, $id, $urls);
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
