<?php
/**
 * This page recieves requests to move scenes.
 */

session_start();
require_once("../shared/constants.php");
/**
 * define autoload for classes
 * throws exception if not found
 */
function __autoload($class_name) {
        require "../interfaces/objectified/$class_name.php";
}

try{
//only posts should be accepted. other verbs are ignored.
if(!empty($_POST)){
    //setup
    //AudioObj::initState();//sets up globals: time and json array
    //get player db info
    $playerInfo = Player::fromDatabase($_POST['playerID']);
    $zone= new Zone($playerInfo['zonex'],
                    $playerInfo['zoney']);
    $dir = $_POST['dir'];
    //set new zone co-ords
    $newZone = null;
    switch($dir){
    case 'N':
        if($zoney >= constants::numZonesSrt){
            throw new Exception("out of map range");
        }
        $newZone = new Zone($zone->posx,
                            $zone->posy + 1);
    case 'S':
        if($zoney <= 0){
            throw new Exception("out of map range");
        }
        $newZone = new Zone($zone->posx,
                            $zone->posy - 1);
    case 'E':
        if($zonex >= constants::numZonesSrt){
            throw new Exception("out of map range");
        }
        $newZone = new Zone($zone->posx + 1,
                            $zone->posy);
    case 'W':
        if($zoney <= 0){
            throw new Exception("out of map range");
        }
        $newZone = new Zone($zone->posx - 1,
                            $zone->posy);
    default:
        throw new Exception("unknown direction");
    }
    //create player obj
    $player = new Player($info['playerID'],//id
                         $newZone,//coords
                         $zone,

                     isset($_POST['ans']) ? $_POST['ans'] : null,//answer
                     $playerInfo);//db info
       //update db info for player
       MainInterface::updatePlayerInfo($player->posx,$player->posy,$player->zone,$player->id);
       //send new zone info if needed
       if($newZone){
       require_once("zoneLoading.php");
       return AudioObj::sendJson();
       exit(0);
       }
       //remove old player events
       MainInterface::removeOldPlayerEvents(AudioObj::$time);
       //get npcs in zone
       $npcResult = MainInterface::getNpcsInZone($player->zone);
       //loop though npcs
       foreach($npcResult as $npcRow){
       $n = new Npc($npcRow['posx'],$npcRow['posy'],$npcRow['id'],$npcRow['finish'], $npcRow['start'], $npcRow['lastAudio']);
       $n->interactPlayer($player);
       }
       
       //get enemies in zone
       $enemyResult = MainInterface::getEnemiesInZone($zone);
       //loop though enemies
       foreach($enemyResult as $enemyRow){
       $e = new Enemy($enemyRow['posx'],$enemyRow['posy'],$enemyRow['id'],$enemyRow['health'], $enemyRow['finish'], $enemyRow['start'], $enemyRow['lastAudio']);
       $e->interactPlayer($player);
       }
       
       //check nearby players
       //check player events
       $eventsResult = MainInterface::getPlayerEventsInZone($zone,$_SESSION['lastupdateTime']);
       foreach($eventsResult as $row){
       AudioObj::addJson(array(
        "event" => true,
        "player" => true,
        "id" => $row['id'],
        "audioType" => $row['audiotype']
       ));
       }
       
       //update last event time
       $_SESSION['lastupdateTime'] = AudioObj::$time;
       
    }
    } else{
        throw new Exception("unknown verb");
    }
} catch(Exception $e){
                ErrorHandler::handle($e);
                    //add exception to json to send
  AudioObj::addJson(array(
     "error" => ($e->getMessage())
  ));
}

////////////////////////////////////////////////////////////////
///////////////////////////--classes--//////////////////////////
////////////////////////////////////////////////////////////////
/**
 *A parent class for all audio objects
 */
class AudioObj {
    public $zone;
    public $id;
    public $busy;//if the obj is currently playing audio
    public $prevAudio;//the numner of the last audio this obj played
    public $prevStart;//when the last audio from this obj started
    public $prevDone;//when the last audio from this obj will be done
    public $objType;//a string which identifies which audioObj type this is
    public static $arrayJSON;//json array to send to clinet
    public static $time;//server time when request was recieved from client
    
    public function __construct($objType, $zone, $id, $prevDone, $prevStart, $prevAudio){
        $this->busy = $prevDone > self::$time;
        $this->objType = $objType;
        $this->zone = $zone;
        $this->id = $id;
        $this->prevDone = $prevDone;
        $this->prevStart = $prevStart;
        $this->prevAudio = $prevAudio;
    }
    
    protected function addEvent($audio){
        $toSend['audioType'] = $audio;
        $toSend['event'] = true;
        $toSend['id'] = $this->id;
        $toSend['posx'] = $this->posx;
        $toSend['posy'] = $this->posy;
        $toSend[$this->objType] = true;
        AudioObj::addJson($toSend);
    }
    
    protected function askQuestion(){
        AudioObj::$arrayJSON[] = (array(
            "question" => true,
            "start" => true
        ));
    }
    
    protected function doneQuestion(){
        AudioObj::$arrayJSON[] = (array(
            "question" => true,
            "done" => true
        ));
    }
    
    protected function findDist($obj){
        $dist = abs($this->posx-$obj->posx);
        $dist2 = abs($this->posy-$obj->posy);
        if($dist > $dist2){
            return $dist;
        }
        return $dist2;
    }
    
    /**
     *finds the zone based off the coords
     */
    public static function findZone($x, $y){
        $zone = floor($x/constants::zoneWidth);
        $zone += constants::numZonesSrt * floor($y/constants::zoneWidth);
        $zone += 1; //zero is null zone
        return $zone;
    }
    
    /**
     *Sends the prev event of this object to the player if needed
     */
    protected function checkEvent(){
        if($_SESSION['lastupdateTime'] < $this->prevStart){
            $this->addEvent($this->prevAudio);
        }
    }
    public static function initState(){
        self::$arrayJSON = array();
        self::$time = time();
    }
    /**
     *Add a json array to the list of json objects to send
     */
    public static function addJson($toAdd){
        self::$arrayJSON[] = $toAdd;
    }
    /**
     *Send the json object to the client
     */
    public static function sendJson(){
        return json_encode(self::$arrayJSON);
    }
}

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

class Npc extends audioObj{
    //aucio ints
    const audio_greet = 0;
    const audio_ask = 1;
    const audio_onYes = 2;
    const audio_onNo = 3;
    
    const dist_talk = 5;
    const dist_notice = 10;

    public function __construct($posx, $posy, $id, $finishTime, $prevStart, $prevAudio){
        parent::__construct("npc", $posx, $posy, $id, $finishTime, $prevStart, $prevAudio);
    }
    
    protected function addEvent($audio){
        MainInterface::addNPCEvent(AudioObj::$time, AudioObj::$time+constants::npcDuration,$audio,$this->id);
        parent::addEvent($audio);
    }
    
    protected function askQuestion(){
        parent::askQuestion();
    }
    
    protected function doneQuestion(){
        parent::doneQuestion();
    }
    
    public function interactPlayer($player){
        //if an event was set after last update
        parent::checkEvent();
        $dist = $this->findDist($player);
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
            $this->addEvent(Npc::audio_greet);
        }
    }
}

class Enemy extends audioObj{
    const audio_notice = 0;
    const audio_attack = 1;
    const audio_death = 2;
    
    const dist_attack = 4;
    
    const max_health = 4;
    private $health;
    
    public function __construct($posx, $posy, $id, $health, $finishTime, $prevStart, $prevAudio){
        parent::__construct("enemy", $posx, $posy, $id, $finishTime, $prevStart, $prevAudio);
        $this->health = $health;
    }
    
    protected function addEvent($audio){
        MainInterface::addEnemyEvent(AudioObj::$time, AudioObj::$time + constants::enemyDuration,$audio, $this->id);
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
        MainInterface::resetEnemy($x,$y,Enemy::max_health,$this->id);
    }
}

/**
 *must be instanciated by player class
 */
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

/**
 * A class to hold zone data
 */
class Zone {
    public $posx;
    public $posy;
    public function __construct($posx, $posy){
        $this->posx = $posx;
        $this->posy = $posy;
    }
}
?>
