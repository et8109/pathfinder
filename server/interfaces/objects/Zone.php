<?php
class Sprite{
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
?>
