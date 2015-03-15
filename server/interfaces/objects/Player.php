<?php
require_once("shared/AudioObj.php");

class Player extends AudioObj{
    const audio_attack = 0;
    const max_health = 4;
    public $health;
    public $sprite;

    /**
     * Should only be initialized from inside the class
     */ 
    protected function __construct($id, $zone, $health){
             parent::__construct(AudioObj::TYPE_PLAYER, 
                $id, $zone, null, null, null);
        $this->ans = $ans;
        $this->health = $health;
        //sprite
        $this->sprite = new Sprite();
    }

    /**
     * Returns a player class for the given player ID
     */
    public static function fromDatabase($playerID){
        $info = PlayerInfo::getInfoById($playerID);
        return new Player(
            $playerID, 
            new Zone($info["zonex"], $info["zoney"]),
            $info["health"]);
    }

    public static function IDfromLogin($uname, $pass){
        $r = PlayerInfo::getInfoLogin($uname, $pass)['id'];
        if($r == null){
            throw new Exception("name/pass combo not found");
        }
    }

    public static function getPrepInfo(){
        $arr = PlayerInfo::getZonePrep($zone->posx, $zone->posy);
        foreach($arr as $n){
            self::addPrepInfo($arr[$n]['id'], $arr[$n]['audioURLs']);
        }
    }
    
    protected function addEvent($audio){
        //TODO override always false
        PlayerEvents::addEvent(AudioObj::$time, AudioObj::$time + constants::playerDuration, $audio, $this->id, $this->zone->posx, $this->zone->posy, false);
    }
    
    /**
     *updates player in db and sends json for reposition
     */
    public function dead(){
        $this->reposition(0,0);
        PlayerInfo::resetPlayer($this->id,self::max_health,0,0);
        //_addPlayerEvent(1, $time, $zone,true);//death sound as event
        $this->sprite->addEvent(Sprite::audio_dead);
    }
    /**
     *repositions the player in the db and sends a json notice
     */
    public function reposition($zone){
        self::addJson(array(
            "playerInfo" => true,
            "zoneX" => $zone->posx,
            "zoneY" => $zone->posy
        ));
    }

    /**
     * Removes all the old events from the log, not just from this player
     */
    public static function removeAllOldEvents($time){
        PlayerEvents::removeExpired($time);
    }
}

class Sprite {
    /**
     *must be instanciated by player class
     */
    public function __construct(){}
    const audio_dead = 0;
    const audio_lowHealth = 1;

    public function getPrepInfo(){
        //TODO
    }
    
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
