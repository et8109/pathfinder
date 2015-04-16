<?php
require_once($_SERVER['DOCUMENT_ROOT']."/server/interfaces/objects/creatures/shared/Creature.php");

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

    private static function getName($type){
        $name = null;
        switch($type){
            case 0:
                $name = "Wolf";
                break;
            default:
                throw new Exception("unknown enemy type");
        }
        require_once($_SERVER['DOCUMENT_ROOT']."/server/interfaces/objects/creatures/enemies/$name.php");
        return $name;
    }

    /**
     * Returns the enemies in the given zone
     */
    public static function getInZone($zone, $getUrls){
        $arr = Enemies::getInZone($zone->zonex, $zone->zoney, $getUrls);
        $list = [];
        foreach($arr as $n){
            $name = self::getName($n['type']);
            $list[] = new $name(
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
