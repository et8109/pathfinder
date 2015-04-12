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

    public function dist(Zone $zone){
        return abs($this->zonex-$zone->zonex) +
               abs($this->zoney-$zone->zoney);

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
}
?>
