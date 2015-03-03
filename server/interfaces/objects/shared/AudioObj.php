<?php
//autoload for objects, which need to load tables
function __autoload($class_name) {
    require "../../database/tables/$class_name.php";
}

/**
 * A parent class for all audio objects
 * Initialized at the bottom of the file.
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
        $toSend['zone'] = $this->zone;
        $toSend[$this->objType] = true;
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
AudioObj::initState();
?>
