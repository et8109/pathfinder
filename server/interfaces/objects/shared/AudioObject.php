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
        self::TYPE_PLAYER => "PlayerInfo",
        self::TYPE_NPC => "Npcs",
        self::TYPE_ENEMY => "Enemies",
        self::TYPE_AMBIENT => "Ambients"
    );

    public $keyid;
    private $type;
    private $urls;
    protected $id;
    private $loading = false;

    protected function __construct($type, $id, $urls){
        $this->keyid = $type . $id;
        $this->type = $type;
        $this->urls = $urls;
        $this->id = $id;
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
    public function addUrls(){
        //return if already loaded
        if($this->loading){
            return;
        }
        if($this->urls == null){
            throw new Exception("unable to load urls");
        }
        global $response;
        $response->add_prep($this->keyid, 
                            $this->urls, 
                            $this->type == self::TYPE_AMBIENT//bool for looping
        );
        $this->loading = true;

        //also play if an ambient
        if($this->type == self::TYPE_AMBIENT){
            addAudio(0);
        }
    }

    protected function getTable(){
        return self::$typeToTable[$this->type];
    }
}
?>
