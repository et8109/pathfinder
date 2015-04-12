<?php
require_once("shared/Enemy.php");

class Wolf extends Enemy{
    const audio_notice = 0;
    const audio_attack = 1;
    const audio_death = 2;
    
    const max_health = 4;
    
    protected function __construct($id, $zone, $health, $urls){
        parent::__construct(TYPE_ENEMY, $id, $zone, $health, $urls);
    }
    
    public function attackPlayer($player){
        //attack audio
        //lower health
        //dead audio or run away audio
        $this->addAudio(self::audio_notice);
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
    protected function dead(){
        //revive elsewhere
        $newZone = $this->zone;
        //check if overlapping with anything
        //set new pos and max health
        Enemies::resetEnemy($newZone->zonex,$newZone->zoney,Enemy::max_health,$this->id);
        $this->addAudio(self::audio_death);
    }

    /**
     * moves to an adjacent zone after a battle if still alive
     */
    protected function retreat(){
        //find adjacent zone
        $newZone = null;
        $dirs = range(0,3);
        shuffle($dirs);
        foreach($dirs as $dir){
            try{
                $newZone = $this->zone->path($dir);
                Enemies::reposition($newZone->zonex, $newZone->zoney, $this->id);
                $this->addAudio(self::audio_attack);
                return;
            } catch(outOfBoundsException $e){
                continue;
            }
        }
        throw new Exception("nowhere for enemy to run");
    }
}
?>
