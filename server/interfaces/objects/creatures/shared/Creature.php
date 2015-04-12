<?php
require_once("../shared/AudioObject.php");

/**
 * A parent class for all audio objects.
 * Things which exist in a single zone.
 */
abstract class Creature extends AudioObject{

    public $zone;//zone that the object is in
    protected $health;//current health of the creature

    protected function __construct($objType, $id, $zone, $health){
        parent::__construct($objType, $id);
        $this->zone = $zone;
        $this->health = $health;
    }

    protected function changeZone($zone){
        getTable()::updateInfo($this->id, $zone->posx, $zone->posy);
    }

    protected abstract function dead();
}
?>
