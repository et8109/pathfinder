<?php
require_once("shared/Enemy.php");

class Wolf extends Enemy{
    const audio_notice = 0;
    const audio_attack = 1;
    const audio_death = 2;
    
    const max_health = 4;
    
    protected function __construct($id, $audios, $zone, $health){
        parent::__construct($id, $audios, $zone, $health);
    }
    
    public function attackPlayer($player){
        //attack audio
        //lower health
        //dead audio or run away audio
        $this->addAudio(self::audio_notice, 0);
        $player->attack($this, 6);//TODO length of attack audio
        if(Enemies::lowerHealth($this->id, $this->zone->zonex, $this->zone->zoney)){
            //enemy dead
            $this->dead(/*t:*/10);
        } else{
            //enemy runs away
            $this->retreat(/*t:*/10);
        }
    }
    /**
     * resets the position in the database
     */
    protected function dead($startTime){
        //revive elsewhere
        $newZone = $this->zone;
        //check if overlapping with anything
        //set new pos and max health
        Enemies::resetEnemy($newZone->zonex,$newZone->zoney,self::max_health,$this->id);
        $this->addAudio(self::audio_death, $startTime);
    }

    /**
     * moves to an adjacent zone after a battle if still alive
     */
    protected function retreat($startTime){
        //find adjacent zone
        $newZone = null;
        $dirs = range(0,3);
        shuffle($dirs);
        foreach($dirs as $dir){
            try{
                $newZone = $this->zone->path($dir);
                Enemies::reposition($newZone->zonex, $newZone->zoney, $this->id);
                $this->addAudio(self::audio_attack,$startTime,
                                $newZone->zonex - $this->zone->zonex,
                                $newZone->zoney - $this->zone->zoney
                                );
                return;
            } catch(outOfBoundsException $e){
                continue;
            }
        }
        throw new Exception("nowhere for enemy to run");
    }
}
?>
