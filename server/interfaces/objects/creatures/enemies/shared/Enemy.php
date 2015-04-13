<?php
require_once("../shared/Creature.php");

abstract class Enemy extends Creature{

    protected $loaded = false;
    
    protected function __construct($id, $urls, $zone, $health, $urls){
        parent::__construct(TYPE_ENEMY, $id, $urls, $zone, $health);
    }


    protected function addUrls(){
        //only load once per class
        if($loaded){
            return;
        }
        $loaded = true;
        parent::adUrls();
    }

    /**
     * Returns the enemies in the given zone
     */
    public static function getInZone($zone, $getUrls){
        $arr = Enemies::getInZone($zone->zonex, $zone->zoney, $getUrls);
        $list = [];
        foreach($arr as $n){
            $list[] = new Wolf(
                $n["id"],
                new Zone($n["zonex"],
                         $n["zoney"]),
                $n["health"],
                $n["urls"]);
        }
        return $list;
    }

    public abstract function attackPlayer($player);
    protected abstract function retreat();

}
?>
