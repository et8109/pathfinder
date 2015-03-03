<?php
require_once("shared/AudioObj.php");

class Player extends AudioObj{
    const audio_attack = 0;
    const max_health = 4;
    public $zone;
    public $zonePrev;
    public $health;
    public $sprite;
    
    public function __construct($id, $newzone, $oldZone, $dbInfo){
        parent::__construct("player", $posx, $posy, $id, null, null, null);
        $this->ans = $ans;
        $this->zone = $zone;
        //from db
        $this->zonePrev = $dbInfo['zone'];
        $this->health = $dbInfo['health'];
        //sprite
        $this->sprite = new Sprite();
    }

    /**
     * Returns a player class for the given player ID
     */
    public static function fromDatabase($playerID){
        return new Player
    }
    
    public function addEvent($audio){
        //TODO o verride always false
        return MainInterface::addPlayerEvent(AudioObj::$time, AudioObj::$time + constants::playerDuration, $audio, $info['playerID'], $this->zone, false);
    }
    
    /**
     *updates player in db and sends json for reposition
     */
    public function dead(){
        $this->reposition(0,0);
        MainInterface::resetPlayer($info['playerID'],Player::max_health,0,0);
        //_addPlayerEvent(1, $time, $zone,true);//death sound as event
        $this->sprite->addEvent(Sprite::audio_dead);
    }
    /**
     *repositions the player in the db and sends a json notice
     */
    public function reposition($x, $y){
        AudioObj::addJson(array(
            "playerInfo" => true,
            "posX" => $x,
            "posY" => $y
        ));
    }
}

class Sprite {
    /**
     *must be instanciated by player class
     */
    public function __construct(){}
    const audio_dead = 0;
    const audio_lowHealth = 1;
    
    public function addEvent($audio){
        //TODO integrate with audioObj
        $toSend = array(
        "spriteEvent" => true,
        "audioType" => $audio
        );
        AudioObj::$arrayJSON[] = $toSend;
    }
}
?>
