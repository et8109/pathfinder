<?php
//autoload for objects, which need to load tables
function __autoload($class_name) {
    require "../../database/tables/$class_name.php";
}

/**
 * A parent class for all audio objects
 * Initialized at the bottom of the file.
 */
abstract class AudioObj {

    const TYPE_PLAYER = 0;
    const TYPE_NPC = 1;
    const TYPE_ENEMY = 2;

    protected $zone;//zone that the object is in
    protected $id; //id of the object
    protected $busy;//if the obj is currently playing audio
    protected $prevAudio;//the numner of the last audio this obj played
    protected $prevStart;//when the last audio from this obj started
    protected $prevDone;//when the last audio from this obj will be done
    protected $objType;//a type which identifies which audioObj type this is
    protected static $arrayJSON;//json array to send to client
    protected static $time;//server time when request was recieved from client

    abstract public function getPrepInfo();//returns info needed before audio can be played
    abstract public static function getInZone($zonex, $zoney);//returns all of the given class in the given zone
    abstract public function fromDatabase($id);//returns the object of the class with the given id
    abstract protected function addEvent($audioID);//adds an event to the outgoing json

    protected function __construct($objType, $id, $zone, $prevDone, $prevStart, $prevAudio){
        $this->busy = $prevDone > self::$time;
        $this->objType = $objType;
        $this->zone = $zone;
        $this->id = $id;
        $this->prevDone = $prevDone;
        $this->prevStart = $prevStart;
        $this->prevAudio = $prevAudio;
    }
    
    protected function addEvent($audio){
        $toSend = [];
        $toSend['audioType'] = $audio;
        $toSend['event'] = true;
        $toSend['id'] = $this->id;
        $toSend['zone'] = $this->zone;
        $toSend[$this->objType] = true;
        self::addJson($toSend);
    }

    /**
     * Info sent to clinet before any audio can be played
     */
    protected function addPrepInfo($id, $audioArr){
        $toSend = [];
        $toSend['prep'] = true;
        $toSend['id'] = $id;
        $toSend['audio'] = [];
        for($audioArr as $a){
            $toSend['audio'][] = $audioArr[$a];
        }
        self::addJson($toSend);
    }
    
    protected function askQuestion(){
        self::$arrayJSON[] = (array(
            "question" => true,
            "start" => true
        ));
    }
    
    protected function doneQuestion(){
        self::$arrayJSON[] = (array(
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
    private static function addJson($toAdd){
        self::$arrayJSON[] = $toAdd;
    }
    /**
     *Send the json object to the client
     */
    public static function sendJson(){
        return json_encode(self::$arrayJSON);
    }
}
AudioObj::initState();
?>
