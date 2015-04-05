<?php
class Zone{

    public $zonex;
    public $zoney;

    public function __construct($zonex, $zoney){
        $this->zonex = $zonex;
        $this->zoney = $zoney;
    }

    public static function addPrepInfo($zone){
        global $response;
        $ids = Ambients::getZonePrep($zone->zonex, $zone->zoney);
        $toSend = [];
        foreach($ids as $a){
            $arr = [];
            $arr['id'] = $a['id'];
            $arr['audio'] = [];
            foreach($a['audioURLs'] as $audio){
                $arr['audio'][] = $audio;
            }
            $response->add_prep_amb($arr);
        }
    }

    public static function addPlayingAmbients($zone){
        global $response;
        $arr = Ambients::getInZone($zone->zonex, $zone->zoney);
        foreach($arr as $a){
            $response->add_play_ambients($a);
        }
    }

    public static function endPrevZone($zone){
        global $response;
        $ids = Ambients::getInZone($zone->zonex, $zone->zoney);
        foreach($ids as $id){
            $a = [];
            $a['id'] = $id['id'];
            $response->add_prep_endAmb($a);
        }
    }
}
?>
