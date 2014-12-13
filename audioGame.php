<?php

require("constants.php");
require("Json.php");
require("mainInterface.php");

try{
    //setup
    /*$posx = $_POST['posx'];
    $posy = $_POST['posy'];
    //set current time
    $time = time();
    //prepare array to send
    $arrayJSON = array();*/
    AudioObj::initState();//sets up globals: time and json array
    //create player object
    $playerInfo = MainInterface::getPlayerInfo($_SESSION['playerID']);
    $player = new Player($_SESSION['playerID'],//id
                         $_POST['posx'], $_POST['posy'],//coords
                         isset($_POST['ans']) ? $_POST['ans'] : null,//answer
                         $playerInfo);//db info
    //send new zone info if needed
    if($player->zonePrev != $player->zone){
        require_once("zoneLoading.php");
        echo json_encode($array);
        exit(0);
    }
    //remove old player events
    MainInterface::removeOldPlayerEvents(AudioObj::$state->time);
    //get npcs in zone
    $npcResult = MainInterface::getNpcsInZone($player->zone);
    //loop though npcs
    foreach($npcResult as $npcRow){
        new Npc($npcRow['posx'],$npcRow['posy'],$npcRow['id'],$npcRow['finish'])->interactPlayer($player);
        if($_SESSION['lastupdateTime'] < $npcRow['start']){
            //if new for this player
            $arrayJSON[] = (array(
                "event" => true,
                "npc" => true,
                "id" => $npcRow['id'],
                "audioType" => $npcRow['lastAudio']
            ));
        }
    }
    
    //get enemies in zone
    MainInterface::getEnemiesInZone($zone);
    //loop though enemies
    foreach($enemyResult as $enemyRow){
        new Enemy($enemyRow['posx'],$enemyRow['posy'],$enemyRow['id'],$enemyRow['health'])->interactPlayer($player);,
        if($_SESSION['lastupdateTime'] < $enemyRow['start']){
            //if new for this player
            $arrayJSON[] = (array(
                "event" => true,
                "enemy" => true,
                "id" => $enemyRow['id'],
                "audioType" => $enemyRow['lastAudio']
            ));
        }
    }
    
    //check nearby players
    //check player events
    MainInterface::getPlayerEventsInZone($zone,$_SESSION['lastupdateTime']);
    foreach($eventsResult as $row){
        $arrayJSON[] =(array(
            "event" => true,
            "player" => true,
            "id" => $row['id'],
            "audioType" => $row['audiotype']
        ));
    }

    //send all info
    echo json_encode($array);
    //update last event time
    $_SESSION['lastupdateTime'] = $time;
    
} catch(Exception $e){
    echo json_encode(array(
        "error" => ($e->getMessage())
    ));
}

public class Npc extends audioObj{
    //aucio ints
    const audio_greet = 0;
    const audio_ask = 1;
    const audio_onYes = 2;
    const audio_onNo = 3;
    
    const dist_talk = 5;
    const dist_notice = 10;

    public function __construct($posx, $posy, $id, $finishTime){
        parent::__construct($posx, $posy, $id, $finishTime);
        $this->posx = $posx;
        $this->posy = $posy;
        $this->id = $id;
    }
    
    private function addEvent($audio){
        MainInterface::addNPCEvent(AudioObj::$state->time, AudioObj::$state->time+constants::npcDuration,$audio,$npc->id);
        $toSend = array();
        $toSend['npc'] = true;
        parent::sendEvent(this, $audio, $toSend);
    }
    
    private function askQuestion(){
        parent::askQuestion();
    }
    
    private function doneQuestion(){
        parent::doneQuestion();
    }
    
    public function interactPlayer($player){
        $dist = this->findDist($player);
        if($dist < Npc::dist_talk && !$this->busy){
            //if answered
            if($player->ans != null){
                if($player->ans == 1){
                    $this->addEvent(Npc::audio_onYes);
                } else if($player->ans == 0){
                    $this->addEvent(Npc::audio_onNo);
                }
                $this->doneQuestion();
            } else{
                //not answered
                $this->addEvent(Npc::audio_ask);
                $this->askQuestion();
            }
        }
        else if($dist < Npc::dist_notice && !$this->busy){
            $this->(Npc::audio_greet);
        }
    }
}

public class Enemy extends audioObj{
    const audio_notice = 0;
    const audio_attack = 1;
    const audio_death = 2;
    
    const dist_attack = 4;
    
    const max_health = 4;
    private final $health;
    
    public function __construct($posx, $posy, $id, $health, $finishTime){
        parent::__construct($posx, $posy, $id, $finishTime);
        $this->posx = $posx;
        $this->posy = $posy;
        $this->id = $id;
        $this->health = $health;
    }
    
    private function addEvent($audio){
        MainInterface::addEnemyEvent($time, $time + constants::enemyDuration,$audio, $id);
        $toSend = array();
        $toSend['enemy'] = true;
        parent::sendEvent(this, $audio, $toSend);
    }
    
    public function interactPlayer($player){
        //if dead
        if($health < 1){
            $this->dead($player->zone);
            return;
        }
        //if alive
        $dist = $this->findDist($player);
        if($dist < Enemy::dist_attack){
            if($player->addEvent(Player::audio_attack,false)){//if player attacks
                //lower monster health
                $dead = MainInterface::lowerEnemyHealth($this->id,$this->posx, $this->posy);
                if($dead){
                    //enemy is killed
                    $this->addEvent(Enemy::audio_death);
                    //query("update enemies set health=3 where id=".prepVar($enemyID)." and posx=".prepVar($x)." and posy=".prepVar($y));
                    //add to kill count
                    MainInterface::increasePlayerKills($_SESSION['playerID']);
                }
            }
            if(!$player->busy){//if enemy attacks
                $this->addEvent(Enemy::audio_attack);
                //lower player health
                MainInterface::lowerPlayerHealth($_SESSION['playerID']);
                //if dead
                if($player->health < 2){
                    //new coords
                    $arrayJSON[] = (array(
                        "playerInfo" => true,
                        "posX" => 0,
                        "posY" => 0
                    ));
                    //update player
                    MainInterface::resetPlayer($_SESSION['playerID'],Player::max_health,0,0);
                    //_addPlayerEvent(1, $time, $zone,true);//death sound as event
                    $player->sprite->addEvent(Sprite::audio_dead);
                    return;
                }
                //if low health
                else if($player->health < 3){
                    $player->sprite->addEvent(Sprite::audio_lowHealth);
                    return;
                }
            } 
        }
        else if($dist < distances::enemyNotice && !$player->busy){
            $this->addEvent(Enemy::audio_notice);
        }
    }
    private function dead($zone){
        //revive elsewhere in zone
        $y = floor(($zone-1)/constants::numZonesSrt);//zone num
        $x = ($zone-1)-($y*constants::numZonesSrt);//zone num
        $y = constants::zoneWidth*$y + rand(constants::zoneBuffer,constants::zoneWidth-constants::zoneBuffer);
        $x = constants::zoneWidth*$x + rand(constants::zoneBuffer,constants::zoneWidth-constants::zoneBuffer);
        //check if overlapping with anything
        //set new pos and max health
        MainInterface::resetEnemy($x,$y,Enemy::max_health,$this->id;
    }
}

/**
 *A parent class for all audio objects
 */
public class AudioObj {
    public final $posx;
    public final $posy;
    public final $id;
    public final $busy;
    public static final $state;//global state
    
    public function __construct($posx, $posy, $id, $finishTime){
        $this->busy = $finishTime > $this->state->time ? True : False;
    }
    
    protected static sendEvent($audioObj, $audio, $toSend){
        $toSend['audioType'] = $audio;
        $toSend['event'] = true;
        $toSend['id'] = $audioObj->id;
        $toSend['posx'] = $audioObj->posx;
        $toSend['posy'] = $audioObj->posy;
        AudioObj::$state->arrayJSON[] = $toSend();
    }
    
    protected static askQuestion(){
        AudioObj::$state->arrayJSON[] = (array(
            "question" => true,
            "start" => true
        ));
    }
    
    protected static doneQuestion(){
        AudioObj::$state->arrayJSON[] = (array(
            "question" => true,
            "done" => true
        ));
    }
    
    protected function findDist($obj){
        $dist = abs($this->posx-$obj->posx);
        $dist2 = abs($this->posx-$obj->posy);
        if($dist > $dist2){
            return $dist;
        }
        return $dist2;
    }
    
    public static function initState(){
     $this->state = new State();  
    }
    
    /**
     *global info relevant to all audio objects
     */
    private static class State {
        public final $arrayJSON;
        public final $time;
        
        public function __construct(){
            $this->arrayJSON = new array();
            $this->time = time();
        }
    }
    
}

public class Player extends AudioObj{
    const audio_attack = 0;
    const max_health = 4;
    public final $ans;
    public final $zone;
    public final $zonePrev;
    public final $health;
    public final $sprite;
    
    public function __construct($id, $posx, $posy, $ans, $dbInfo){
        parent::__construct($posx, $posy, $id, 0);
        //from client
        this->playerX = $posx;
        this->playerY = $posy;
        this->ans = $ans;
        //find current zone
        $zone = floor($posx/constants::zoneWidth);
        $zone += constants::numZonesSrt * floor($posy/constants::zoneWidth);
        $zone += 1; //zero is null zone
        this->$zone = $zone;
        //from db
        this->zonePrev = $dbInfo['zone'];
        this->health = $dbInfo['health'];
        //sprite
        this->sprite = new Sprite();
    }
    
    public function addEvent($audio, $override){
        return MainInterface::addPlayerEvent(AudioObj::$state->time, AudioObj::$state->time + constants::playerDuration, $audio, $_SESSION['playerID'], $this->zone, $override);
    }
    
    private class Sprite {
        public function __construct(){}
        const audio_dead = 0;
        const audio_lowHealth = 1;
        
        public function addEvent($audio){
            //TODO integrate with audioObj
            $toSend = array(
            "spriteEvent" => true,
            "audioType" => $audioType
            );
            AudioObj::$state->arrayJSON[] = $toSend();
        }
    }
    
}




?>