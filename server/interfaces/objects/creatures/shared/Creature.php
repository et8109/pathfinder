<?php
require_once( $_SERVER['DOCUMENT_ROOT']."/server/interfaces/objects/shared/AudioObject.php");

/**
 * A parent class for all audio objects.
 * Things which exist in a single zone.
 */
abstract class Creature extends AudioObject{

    public $zone;//zone that the object is in
    protected $health;//current health of the creature

    protected function __construct($id, $urls,Zone $zone, $health){
        parent::__construct($id, $urls);
        $this->zone = $zone;
        $this->health = $health;
    }

    protected function changeZone($zone){
        $table = self::getTable();
        $table::updateInfo($this->id, $zone->zonex, $zone->zoney);
        $this->zone = $zone;
    }

    protected abstract function dead($startTime);
}
?>
