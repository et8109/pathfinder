<?php
//autoload for objects, which need to load tables
//
//
spl_autoload_register(function ($class_name) {
    $file = $_SERVER['DOCUMENT_ROOT']."/server/database/tables/$class_name.php";
    if (file_exists($file)) {
        include_once($file);
    }
}, true, true);

/**
 * A parent class for all audio objects
 * Initialized at the bottom of the file.
 */
abstract class AudioObj {

    const TYPE_PLAYER = 'p';
    const TYPE_NPC = 'n';
    const TYPE_ENEMY = 'e';

    public $zone;//zone that the object is in
    protected $id; //id of the object
    protected $busy;//if the obj is currently playing audio
    protected $prevAudio;//the numner of the last audio this obj played
    protected $prevStart;//when the last audio from this obj started
    protected $prevDone;//when the last audio from this obj will be done
    protected $objType;//a type which identifies which audioObj type this is
    //abstract static public function getPrepInfo();//returns info needed before audio can be played
    //abstract public static function getInZone($zonex, $zoney);//returns all of the given class in the given zone
    //abstract public static function fromDatabase($id);//returns the object of the class with the given id
    //TODO wont let me se them to asbtract

    protected function __construct($objType, $id, $zone, $prevDone, $prevStart, $prevAudio){
        $this->busy = null;//$prevDone > $_response->time;TODO
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
        $toSend['id'] = $this->id;
        $toSend['zone'] = $this->zone;
        return $toSend;
    }

    /**
     * Info sent to clinet before any audio can be played
     */
    protected static function addPrepInfo($type, $id, $audioArr){
        $toSend = [];
        $toSend['id'] = $id;
        $toSend['audio'] = [];
        foreach($audioArr as $a){
            $toSend['audio'][] = $a;
        }
        return $toSend;
    }
    
    protected function askQuestion(){
        return array(
            "question" => true,
            "start" => true
        );
    }
    
    protected function doneQuestion(){
        return array(
            "question" => true,
            "done" => true
        );
    }
    
    protected function findDist($obj){
        $dist = abs($this->zone->zonex-$obj->zone->zonex);
        $dist2 = abs($this->zone->zoney-$obj->zone->zoney);
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
            return $this->addEvent($this->prevAudio);
        }
    }
}
?>
