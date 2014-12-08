<?php

require_once("constants.php");
require_once("Json.php");
require_once("mainInterface.php");

function addNpcEvent($npc, $px,$py,$time,$busy,&$arrayJSON,$ans){
    //distance from player
    $dist = findDist($px,$py,$npc->$posx,$npc->$posy);
    if($dist < distances::personTalk && !$busy){
        //if answered
        if(isset($ans)){
            if($ans == 1){
                _addNpcEvent(2,$npc,$time,$px,$py,$arrayJSON);//yes
            } else if($ans == 0){
                _addNpcEvent(3,$npc,$time,$px,$py,$arrayJSON);//no
            }
            doneQuestion($arrayJSON);
        } else{
            //not answered
            _addNpcEvent(1,$npc,$time,$px,$py,$arrayJSON);//ask q
            askQuestion($arrayJSON);
        }
    }
    else if($dist < distances::personNotice && !$busy){
        _addNpcEvent(0,$npc,$time,$px,$py,$arrayJSON);//welcome
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
function _addNpcEvent($audio,$npc,$time,$px,$py,&$arrayJSON){
    MainInterface::addNPCEvent($time, $time+constants::npcDuration,$audio,$npc->id)
    $arrayJSON[] = (array(
        "event" => true,
        "npc" => true,
        "id" => $npc->id,
        "posx" => $px,
        "posy" => $py,
        "audioType" => $audio
    ));
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
    $posx = $_POST['posx'];
    $posy = $_POST['posy'];
    //prepare array to send
    $arrayJSON = array();
    //check if player sent a reply
    $ans = null;
    if(isset($_POST['ans'])){
        $ans = $_POST['ans'];
    }
    //find current zone
    $zone = floor($posx/constants::zoneWidth);
    $zone += constants::numZonesSrt * floor($posy/constants::zoneWidth);
    $zone += 1; //zero is null zone
    //check if zone change, load if new
    $playerInfo = MainInterface::getPlayerInfo($_SESSION['playerID']);
    $newZone = false;
    if($playerInfo['zone'] != $zone){
        require_once("zoneLoading.php");
        echo json_encode($array);
    }
    //set current time
    $time = time();
    //remove old player events
    MainInterface::removeOldPlayerEvents($time);
    //get npcs in zone
    $npcResult = MainInterface::getNpcsInZone($zone);
    //loop though npcs
    foreach($npcResult as $npcRow){
        addNpcEvent(new Npc($npcRow['posx'],$npcRow['posy'],$npcRow['id']),
                    $posx, $posy, $time, $time < $npcRow['finish'],$arrayJSON,$ans);
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

public class Npc {
    public final $posx;
    public final $posy;
    public final $id;
    public function __construct($posx, $posy, $id){
        $this->posx = $posx;
        $this->posy = $posy;
        $this->id = $id;
    }
}

public class Enemy {
    public final $posx;
    public final $posy;
    public final $id;
    public final $health;
    
    public function __construct($posx, $posy, $id, $health){
        $this->posx = $posx;
        $this->posy = $posy;
        $this->id = $id;
        $this->health = $health;
    }
}


?>