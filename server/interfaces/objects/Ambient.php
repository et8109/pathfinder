<?php

require_once("shared/AudioObject.php");

class Ambient extends AudioObject{

    static $type = TYPE_AMBIENT;

    protected function __construct($id, $audios){
        parent::__construct($id, $audios);
    }


    protected static function fromDbRow($row){
        return new Ambient($row['id'], self::audiosFromDbRow($row));
    }
}
?>
