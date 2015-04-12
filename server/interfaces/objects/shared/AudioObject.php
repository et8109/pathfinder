<?php

//autoload for audio classes, which need to load tables
spl_autoload_register(function ($class_name) {
    $file = $_SERVER['DOCUMENT_ROOT']."/server/database/tables/$class_name.php";
    if (file_exists($file)) {
        include_once($file);
    }
}, true, true);

/**
 * A class for anything that creates audio.
 */
abstract class AudioObject{

    const TYPE_PLAYER = 'p';
    const TYPE_NPC = 'n';
    const TYPE_ENEMY = 'e';
    const TYPE_AMBIENT = 'a';
    const TYPE_SPRITE = 's';

    private static $typeToTable = array(
        TYPE_PLAYER => "PlayerInfo",
        TYPE_NPC => "Npcs",
        TYPE_ENEMY => "Enemies",
        TYPE_AMBIENT => "Ambients"
    );

    private $keyid;
    private $type;

    protected function __construct($type, $id){
        $this->keyid = $type . $id;
        $this->type = $type;
    }

    /**
     * Adds the given audio data to the response to the client
     */
    protected function addAudio($num){
        global $response;
        $response->add_play($this->keyid, $num);
    }

    /**
     * Send audio info to the client
     */
    protected function addPrepInfo($url){
        global $response;
        $response->add_prep($this->keyid, $url);
    }

    protected function getTable(){
        return $typeToTable[$this->type];
    }
}
?>
