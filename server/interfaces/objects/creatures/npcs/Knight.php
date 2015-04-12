<?php
require_once("shared/Npc.php");

class Knight extends Npc{
    const audio_greet = 0;
    const audio_ask = 1;
    const audio_onYes = 2;
    const audio_onNo = 3;
    
    const dist_talk = 5;
    const dist_notice = 10;

    protected function __construct($id, $zone, $health, $urls){
        parent::__construct(AudioObj::TYPE_NPC, $id, $zone, $health, $urls);
    }
    
    public function interactPlayer($player){
        //if an event was set after last update
        //parent::checkEvent();

        if(!$this->busy){
            $this->addAudio(Npc::audio_greet);
        }

        /*if($dist < Npc::dist_talk && !$this->busy){
            //if answered
            echo $player->ans;
            if($player->ans != null){
                if($player->ans == 1){
                    return $this->addEvent(Npc::audio_onYes);
                } else if($player->ans == 0){
                    return $this->addEvent(Npc::audio_onNo);
                }
                $this->doneQuestion();
            } else{
                //not answered
                return $this->addEvent(Npc::audio_ask);
                $this->askQuestion();
            }
        }
        else if($dist < Npc::dist_notice && !$this->busy){
            return $this->addEvent(Npc::audio_greet);
        }*/
    }
}
?>
