<?php
require_once("../shared/Creature.php");

abstract class Npc extends Creature{

    private $urls;

    protected function __construct($id, $zone, $health, $urls){
        parent::__construct(AudioObj::TYPE_NPC, $id, $zone, $health);
        $this->urls = $urls;
    }
    
    public static function interactPlayer($player);

    /**
     * Returns the prep info needed when entering a new scene for each npc.
     */
    public function addPrepInfo($zone){
        parent::sendPrepInfo(TYPE_NPC, $this->urls);
    }

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
