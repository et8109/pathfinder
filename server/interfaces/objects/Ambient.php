<?php

require_once("shared/AudioObject.php")

class Ambient extends AudioObject{

    protected function __construct($id, $urls){
        parent::__construct(TYPE_AMBIENT, $id, $urls);
    }

    /**
     * Returns ambients in the given zone. With audio urls if set to true
     */
    public static function getInZone($zone, $getUrls){
        $arr = Ambients::getInZone($zone->zonex, $zone->zoney, $getUrls);
        $ambients = [];
        if($getUrls){
           for($arr as $a){
                $ambients[] = new Ambient($a['id'], $a['urls']);
            }
        } else {
            for($arr as $a){
                $ambients[] = new Ambient($a['id'], null);
            }
        }
        return $ambients;
    }
}

?>
