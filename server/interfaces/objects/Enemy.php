<?php
require_once("shared/AudioObj.php");

class Enemy extends AudioObj{
    const audio_notice = 0;
    const audio_attack = 1;
    const audio_death = 2;
    
    const dist_attack = 4;
    
    const max_health = 4;
    private $health;
    
    public function __construct($id, $zone, $health, $finishTime, $prevStart, $prevAudio){
        parent::__construct(AudioObj::TYPE_ENEMY, $id, $zone, $finishTime, $prevStart, $prevAudio);
        $this->health = $health;
    }
    
    protected function addEvent($audio){
        Enemies::addEvent(AudioObj::$time, AudioObj::$time + constants::enemyDuration,$audio, $this->id);
        parent::addEvent($audio);
    }
    
    public function interactPlayer($player){
        //if an event was set after last update
        parent::checkEvent();
        //if dead
        if($this->health < 1){
            $this->dead($player->zone);
            return;
        }
        //if alive
        $dist = $this->findDist($player);
        if($dist < Enemy::dist_attack){
            if($player->addEvent(Player::audio_attack)){//if player attacks
                //lower monster health
                $dead = MainInterface::lowerEnemyHealth($this->id,$this->posx, $this->posy);
                if($dead){
                    //enemy is killed
                    $this->addEvent(Enemy::audio_death);
                    //add to kill count
                    MainInterface::increasePlayerKills($info['playerID']);
                }
            }
            if(!$this->busy){//if enemy attacks
                $this->addEvent(Enemy::audio_attack);
                //lower player health
                MainInterface::lowerPlayerHealth($info['playerID']);
                //if dead
                if($player->health < 2){
                    $player->dead();
                    return;
                }
                //if low health
                else if($player->health < 3){
                    $player->sprite->addEvent(Sprite::audio_lowHealth);
                    return;
                }
            } 
        }
        else if($dist < distances::enemyNotice && !$this->busy){
            $this->addEvent(Enemy::audio_notice);
        }
    }
    /**
     *resets the position in the database
     */
    private function dead($zone){
        //revive elsewhere in zone
        $y = floor(($zone-1)/constants::numZonesSrt);//zone num
        $x = ($zone-1)-($y*constants::numZonesSrt);//zone num
        $y = constants::zoneWidth*$y + rand(constants::zoneBuffer,constants::zoneWidth-constants::zoneBuffer);
        $x = constants::zoneWidth*$x + rand(constants::zoneBuffer,constants::zoneWidth-constants::zoneBuffer);
        //check if overlapping with anything
        //set new pos and max health
        Enemies::resetEnemy($x,$y,Enemy::max_health,$this->id);
    }

    /**
     * Returns the prep info needed when entering a new scene for each enemy.
     */
    public static function getPrepInfo($zone){
        $arr = Enemies::getZonePrep($zone->posx, $zone->posy);
        for($arr as $n){
            self::addPrepInfo($arr[$n]['type'], $arr[$n]['audioURLs']);
        }
    }

    /**
     * Returns the enemies in the given zone
     */
    public static function getInZone($zone){
        $arr = Enemies::getInZone($zone->posx, $zone->posy);
        $list = [];
        for($arr as $n){
            $list[] = new Enemy(
                $arr[$n]["id"],
                new Zone($arr[$n]["zonex"],
                         $arr[$n]["zoney"]),
                $arr[$n]["health"],
                $arr[$n]["finish"],
                $arr[$n]["start"],
                $arr[$n]["lastAudio"]);
        }
    }

}
?>
