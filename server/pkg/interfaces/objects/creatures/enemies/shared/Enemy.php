<?php
require_once($_SERVER['DOCUMENT_ROOT']."/server/interfaces/objects/creatures/shared/Creature.php");

abstract class Enemy extends Creature{

    public static $type = self::TYPE_ENEMY;

    protected static $loaded = false;
    
    protected function __construct($id, $audios, Zone $zone, $health){
        parent::__construct($id, $audios, $zone, $health);
    }


    public function addUrls(){
        //only load once per class
        if(self::$loaded){
            return;
        }
        self::$loaded = true;
        parent::addUrls();
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



    protected static function fromDbRow($row){
        $name = self::getName($row['type']);
        return new $name($row['id'],
            self::audiosFromDbRow($row),
            new Zone($row['zonex'], $row['zoney']),
            $row['health']);
    }

    public abstract function attackPlayer($player);
    protected abstract function retreat($startTime);

}
?>
