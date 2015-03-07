<?php
require_once("shared/AudioObj.php");

class Npc extends AudioObj{
    //aucio ints
    const audio_greet = 0;
    const audio_ask = 1;
    const audio_onYes = 2;
    const audio_onNo = 3;
    
    const dist_talk = 5;
    const dist_notice = 10;

    public function __construct($id, $zone, $finishTime, $prevStart, $prevAudio){
        parent::__construct(AudioObj::TYPE_NPC, $id, $zone, $finishTime, $prevStart, $prevAudio);
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
    
    /**
     * Returns the prep info needed when entering a new scene for each npc.
     */
    public static function getPrepInfo($zone){
        Npcs::getZonePrep($zone->posx, $zone->posy);
    }

    public static function getUpdateInfo($zone){
        $arr = Npcs::getZoneUpdate($zone->posx, $zone->posy);
        $list = [];
        for($arr as $n){
            $list[] = new Npc(
                $arr[$n]["id"],
                new Zone($arr[$n]["zonex"],
                         $arr[$n]["zoney"]),
                $arr[$n]["finish"],
                $arr[$n]["start"], 
                $arr[$n]["lastAudio"]);
        }
    }
}
?>
