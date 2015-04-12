<?php

require_once("shared/AudioObject.php")

class Ambient extends AudioObject{

    private $audioUrls;

    protected function __construct($id, $audioUrls){
        parent::__construct(TYPE_AMBIENT, $id);
        $this->audioUrls = $audioUrls;
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

    public function addPrepInfo(){
        if($this->audioUrls == null){
            throw new Exception("partial ambient cannot be prepped");
        }

        for($this->ambientUrls as $u){
            parent::addPrepInfo($u);
        }
    }

    public function addPlaying($zone){//TODO assumes it is always playing
        addAudio(0);
    }

    /*public static function endPrevZone($zone){
        global $response;
        $ids = Ambients::getInZone($zone->zonex, $zone->zoney);
        foreach($ids as $id){
            $a = [];
            $a['id'] = $id['id'];
            $response->add_prep_endAmb($a);
        }
    }*/

}

?>
