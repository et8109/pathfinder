<?php
class Zone{

    public $zonex;
    public $zoney;

    const dir_north = 0;
    const dir_east = 1;
    const dir_south = 2;
    const dir_west = 3;

    public function __construct($zonex, $zoney){
        $this->zonex = $zonex;
        $this->zoney = $zoney;
    }

    /**
     * Returns a zone object in the given direction of this zone
     */ 
    public function path($dir){
        $new = null;
        switch($dir){
        case self::dir_north:
            $new = new Zone($this->zonex,
                            $this->zoney + 1);
            break;
        case self::dir_east:
            $new = new Zone($this->zonex + 1,
                            $this->zoney);
            break;
        case self::dir_south:
            $new = new Zone($this->zonex,
                            $this->zoney - 1);
            break;
        case self::dir_west:
            $new = new Zone($this->zonex - 1,
                            $this->zoney);
            break;
        default:
            throw new Exception("unknown direction");
        }
        if(self::isOutOfBounds($new)){
            throw new outOfBoundsException("zone out of bounds");
        }
        return $new;
    }

    private static function isOutOfBounds($zone){
        if($zone->zonex > constants::numZonesSrt || 
           $zone->zoney > constants::numZonesSrt ||
           $zone->zonex < 0 ||
           $zone->zoney < 0){
               return true;
           }
        return false;
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
