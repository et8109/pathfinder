<?php

require_once($_SERVER['DOCUMENT_ROOT']."/server/interfaces/objects/creatures/shared/Creature.php");

abstract class Npc extends Creature{

    protected function __construct($id, $urls, $zone, $health){
        parent::__construct(AudioObj::TYPE_NPC, $id, $urls, $zone, $health);
    }

    public abstract function interactPlayer($player);

    private static function getName($id){
        $name = null;
        switch($id){
            case 0:
                $name = "Knight";
                break;
            default: 
                throw new Exception("unknown npc id");
        }
        require_once($_SERVER['DOCUMENT_ROOT']."/server/interfaces/objects/creatures/npcs/$name.php");
        return $name;
    }


    /**
     * Returns the npcs in the given zone
     */
    public static function getInZone($zone, $getUrls){
        $arr = Npcs::getInZone($zone->zonex, $zone->zoney, $getUrls);
        echo var_dump($arr);
        $list = [];
        foreach($arr as $n){
            $name = self::getName($n['id']);
            $list[] = new $name(
                $n["id"],
                new Zone($n["zonex"],
                    $n["zoney"]),
                $n['health'],
                $n["urls"]);
        }
        return $list;
    }
}
?>
