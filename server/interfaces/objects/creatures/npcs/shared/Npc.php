<?php
require_once("../shared/Creature.php");

abstract class Npc extends Creature{

    protected function __construct($id, $urls, $zone, $health){
        parent::__construct(AudioObj::TYPE_NPC, $id, $urls, $zone, $health);
    }
    
    public static function interactPlayer($player);

    /**
     * Returns the npcs in the given zone
     */
    public static function getInZone($zone, $getUrls){
        $arr = Npcs::getInZone($zone->zonex, $zone->zoney, $getUrls);
        $list = [];
        foreach($arr as $n){
            $list[] = new Npc(
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
