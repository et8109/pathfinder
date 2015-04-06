<?php
require_once("shared/AudioObj.php");

class Enemy extends AudioObj{
    const audio_notice = 0;
    const audio_attack = 1;
    const audio_death = 2;
    
    const dist_attack = 4;
    
    const max_health = 4;
    private $health;
    
    protected function __construct($id, $zone, $health, $finishTime, $prevStart, $prevAudio){
        parent::__construct(AudioObj::TYPE_ENEMY, $id, $zone, $finishTime, $prevStart, $prevAudio);
        $this->health = $health;
    }
    
    protected function addEvent($audio){
        global $_timeRecieved;
        global $response;
        Enemies::addEvent($_timeRecieved, $_timeRecieved + constants::enemyDuration,$audio, $this->id);
        $response->add_play_enemies(parent::addEvent($audio));
    }
    
    public function interactPlayer($player){
        //attack audio
        //lower health
        //dead audio or run away audio
        $this->addEvent(self::audio_notice);
        $player->attack($this);
        if(Enemies::lowerHealth($this->id, $this->zone->zonex, $this->zone->zoney)){
            //enemy dead
            $this->dead();
        } else{
            //enemy runs away
            $this->runAway();
        }
    }
    /**
     * resets the position in the database
     */
    private function dead(){
        //revive elsewhere
        $newZone = $this->zone;
        //check if overlapping with anything
        //set new pos and max health
        Enemies::resetEnemy($newZone->zonex,$newZone->zoney,Enemy::max_health,$this->id);
        $this->addEvent(self::audio_death);
    }

    /**
     * moves to an adjacent zone after a battle if still alive
     */
    private function runAway(){
        //find adjacent zone
        $newZone = null;
        $dirs = range(0,3);
        shuffle($dirs);
        foreach($dirs as $dir){
            try{
                $newZone = $this->zone->path($dir);
                Enemies::reposition($newZone->zonex, $newZone->zoney, $this->id);
                $this->addEvent(self::audio_attack);
                return;
            } catch(outOfBoundsException $e){
                continue;
            }
        }
        throw new Exception("nowhere for enemy to run");
    }

    /**
     * Returns the prep info needed when entering a new scene for each enemy.
     */
    public static function getPrepInfo($zone){
        global $response;
        $arr = Enemies::getZonePrep($zone->zonex, $zone->zoney);
        foreach($arr as $e){
            $response->add_prep_enemies($e);
        }
    }

    /**
     * Returns the enemies in the given zone
     */
    public static function getInZone($zone){
        $arr = Enemies::getInZone($zone->zonex, $zone->zoney);
        $list = [];
        foreach($arr as $n){
            $list[] = new Enemy(
                $n["id"],
                new Zone($n["zonex"],
                         $n["zoney"]),
                $n["health"],
                $n["finish"],
                $n["start"],
                $n["lastAudio"]);
        }
        return $list;
    }

}
?>
