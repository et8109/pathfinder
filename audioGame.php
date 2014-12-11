<?php

require_once("constants.php");
require_once("Json.php");
require_once("mainInterface.php");

function addNpcEvent($npc,$player){
    //distance from player
    $dist = findDist($player->posx,$player->posy,$npc->posx,$npc->posy);
    if($dist < distances::personTalk && !$busy){
        //if answered
        if(isset($ans)){
            if($ans == 1){
                $npc->addEvent(Npc::audio_onYes);
            } else if($ans == 0){
                $npc->addEvent(Npc::audio_onNo);
            }
            doneQuestion($arrayJSON);
        } else{
            //not answered
            $npc->addEvent(Npc::audio_ask);
            askQuestion($arrayJSON);
        }
    }
    else if($dist < distances::personNotice && !$busy){
        $npc->(Npc::audio_greet);
    }
}

function addEnemyEvent($enemy, $px,$py, $time,/*player:*/$zone,$health,$busy,&$arrayJSON){
    $dist = findDist($px,$py,$enemt->posx,$enemy->posy);
    if($dist < distances::enemyAttack){
        if(_addPlayerEvent(0,$time, $zone,false)){//if player attacks
            //lower monster health
            $dead = MainInterface::lowerEnemyHealth($enemy->id, $x, $y);
            if($dead){
                //enemy is killed
                _addEnemyEvent(2, $enemy, $time, $arrayJSON);//death audio
                //query("update enemies set health=3 where id=".prepVar($enemyID)." and posx=".prepVar($x)." and posy=".prepVar($y));
                //add to kill count
                MainInterface::increasePlayerKills($_SESSION['playerID']);
            }
        }
        if(!$busy){//if enemy attacks
            _addEnemyEvent(1, $enemy, $time, $arrayJSON);//attacking
            //lower player health
            MainInterface::lowerPlayerHealth($_SESSION['playerID']);
            //if dead
            if($health < 2){
                //new coords
                $arrayJSON[] = (array(
                    "playerInfo" => true,
                    "posX" => 0,
                    "posY" => 0
                ));
                //update player
                MainInterface::resetPlayer($_SESSION['playerID'],constants::maxHealth,0,0);
                //_addPlayerEvent(1, $time, $zone,true);//death sound as event
                _addSpriteEvent(1, $arrayJSON);//you're dead msg
                return;
            }
            //if low health
            else if($health < 3){
                _addSpriteEvent(0, $arrayJSON);//low health msg
                return;
            }
        } 
    }
    else if($dist < distances::enemyNotice && !$busy){
        _addEnemyEvent(0, $enemy, $time, $arrayJSON);//notice audio
    }
}

/**
 *overrides current event
 */
function _addNpcEvent($npc, $audio){
    
}
/**
 *overrides current event
 */
function _addEnemyEvent($audio,$enemy,$time,&$arrayJSON){
    MainInterface::addEnemyEvent($time, $time + constants::enemyDuration,$audio, $id);
    $arrayJSON[] = (array(
        "event" => true,
        "enemy" => true,
        "id" => $enemy->id,
        "posx" => $enemy->posx,
        "posy" => $enemy->posy,
        "audioType" => $audio
    ));
}

/**
 *returns false is player is busy
 *true if event added
 */
function _addPlayerEvent($audio,$time,$zone, $override){
    return MainInterface::addPlayerEvent($time, $time + constants::playerDuration, $audio, $_SESSION['playerID'], $zone, $override);
}

/**
 *sprite events can only be heard by the player
 */
function _addSpriteEvent($audioType,&$arrayJSON){
    $arrayJSON[] = (array(
        "spriteEvent" => true,
        "audioType" => $audioType
    ));
}

/**
 *requests a yes or no from the player
 */
function askQuestion(&$arrayJSON){
    $arrayJSON[] = (array(
        "question" => true,
        "start" => true
    ));
}
/**
 *tells js to remove the current answer
 */
function doneQuestion(&$arrayJSON){
    $arrayJSON[] = (array(
        "question" => true,
        "done" => true
    ));
}

function findDist($px,$py,$x,$y){
    $dist = abs($px-$x);
    $dist2 = abs($py-$y);
    if($dist > $dist2){
        return $dist;
    }
    return $dist2;
}

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
        addNpcEvent(new Npc($npcRow['posx'],$npcRow['posy'],$npcRow['id'],$npcRow['finish']),
                    $player);
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
        //if dead
        if($enemyRow['health'] <= 0){
            //revive elsewhere in zone
            $y = floor(($zone-1)/constants::numZonesSrt);//zone num
            $x = ($zone-1)-($y*constants::numZonesSrt);//zone num
            $y = constants::zoneWidth*$y + rand(constants::zoneBuffer,constants::zoneWidth-constants::zoneBuffer);
            $x = constants::zoneWidth*$x + rand(constants::zoneBuffer,constants::zoneWidth-constants::zoneBuffer);
            //check if overlapping with anything
            //set new pos and max health
            MainInterface::resetEnemy($x,$y,constants::maxHealth,$enemyRow['id']);
        } else{
            //if alive
            addEnemyEvent(new Enemy($enemyRow['posx'],$enemyRow['posy'],$enemyRow['id'],$enemyRow['health']),
                          $posx, $posy, $time, $zone, $playerInfo['health'], $time < $enemyRow['finish'], $arrayJSON);
            new Enemy();
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

    public function __construct($posx, $posy, $id, $finishTime){
        parent::__construct($posx, $posy, $id, $finishTime);
        $this->posx = $posx;
        $this->posy = $posy;
        $this->id = $id;
    }
    
    public function addEvent($audio){
        MainInterface::addNPCEvent(AudioObj::$state->time, AudioObj::$state->time+constants::npcDuration,$audio,$npc->id);
        $toSend = array();
        $toSend['npc'] = true;
        parent::sendEvent(this, $audio, $toSend);
    }
    
}

public class Enemy extends audioObj{
    public final $health;
    
    public function __construct($posx, $posy, $id, $health, $finishTime){
        parent::__construct($posx, $posy, $id, $finishTime);
        $this->posx = $posx;
        $this->posy = $posy;
        $this->id = $id;
        $this->health = $health;
    }
}

/**
 *A parent class for all audio objects
 */
public class AudioObj {
    public final $posx;
    public final $posy;
    public final $id;
    public static final $state;//global state
    public final $busy;
    
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
    public final $ans;
    public final $zone;
    public final $zonePrev;
    public final $health;
    
    public function __construct($id, $posx, $posy, $ans, $dbInfo){
        parent::__construct($posx, $posy, $id, 0);
        //from client
        $this->playerX = $posx;
        $this->playerY = $posy;
        $this->ans = $ans;
        //find current zone
        $zone = floor($posx/constants::zoneWidth);
        $zone += constants::numZonesSrt * floor($posy/constants::zoneWidth);
        $zone += 1; //zero is null zone
        $this->$zone = $zone;
        //from db
        $this->zonePrev = $dbInfo['zone'];
        $this->health = $dbInfo['health'];
    }
}




?>