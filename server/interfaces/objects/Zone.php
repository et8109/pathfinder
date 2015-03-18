<?php
class Zone{

    public $zonex;
    public $zoney;

    public function __construct($zonex, $zoney){
        $this->zonex = $zonex;
        $this->zoney = $zoney;
    }

    public static function getPrepInfo($zone){
        $arr = Ambients::getZonePrep($zone->zonex, $zone->zoney);
        foreach($arr as $a){
            $toSend = [];
            $toSend['prep'] = true;
            $toSend['type'] = "a";
            $toSend['id'] = $a['id'];
            $toSend['audio'] = [];
            foreach($a['audioURLs'] as $audio){
                $toSend['audio'][] = $audio;
            }
            Translator::add($toSend);
        }
    }
}
?>
