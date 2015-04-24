<?php

require_once($_SERVER['DOCUMENT_ROOT']."/server/interfaces/objects/creatures/shared/Creature.php");

abstract class Npc extends Creature{

    public static $type = self::TYPE_NPC;

    protected function __construct($id, $audios, Zone $zone, $health){
        parent::__construct($id, $audios, $zone, $health);
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


    protected static function fromDbRow($row){
        $name = self::getName($row['id']);
        return new $name($row['id'],
            self::audiosFromDbRow($row),
            new Zone($row['zonex'], $row['zoney']),
            $row['health']);
    }
}
?>
