<?php
class Zone{

    public $zonex;
    public $zoney;

    public function __construct($zonex, $zoney){
        $this->zonex = $zonex;
        $this->zoney = $zoney;
    }

    public static function getPrepInfo($zone){
        $ids = Ambients::getZonePrep($zone->zonex, $zone->zoney);
        $toSend = [];
        foreach($ids as $a){
            $arr = [];
            $arr['id'] = $a['id'];
            $arr['audio'] = [];
            foreach($a['audioURLs'] as $audio){
                $arr['audio'][] = $audio;
            }
            $toSend[] = $arr;
        }
        return $toSend;
    }

    public static function getPlayingAmbients($zone){
        return Ambients::getInZone($zone->zonex, $zone->zoney);
    }

    public static function endPrevZone($zone){
        $ids = Ambients::getInZone($zone->zonex, $zone->zoney);
        $toSend = [];
        foreach($ids as $id){
            $a = [];
            $a['id'] = $id['id'];
            $toSend[] = $a;
        }
        return $toSend;
    }
}
?>
