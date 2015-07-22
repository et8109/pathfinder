<?php

//autoload for audio classes, which need to load tables
spl_autoload_register(function ($class_name) {
    $file = $_SERVER['DOCUMENT_ROOT']."/server/pkg/database/tables/$class_name.php";
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
    protected $audios;//num-> url, length
    protected $id;
    private $loading = false;

    //static::$type declared in child classes

    protected function __construct($id, $audios){
        $this->keyid = static::$type . $id;
        $this->audios = $audios;
        $this->id = $id;
    }

    /**
     * Adds the given audio data to the response to the client.
     * Returns the end time
     */
    protected function addAudio($num, $time, $dirx = 0, $diry = 0){
        global $response;
        $response->add_play($this->keyid, $num, $time, $dirx, $diry);
        return $time + $this->audios[$num]->length;
    }

    /**
     * Send audio info to the client
     */
    public function addUrls(){
        //return if already loaded
        if($this->loading){
            return;
        }
        if($this->audios == null){
            throw new Exception("unable to load urls");
        }
        $urls = [];
        foreach($this->audios as $a){
            $urls[] = $a->url;
        }
        global $response;
        $response->add_prep($this->keyid, 
                            $urls, 
                            static::$type == self::TYPE_AMBIENT//bool for looping
        );
        $this->loading = true;

        //also play if an ambient
        if(static::$type == self::TYPE_AMBIENT){
            $this->addAudio(0, 0);
        }
    }

    /**
     * returnsa list of this object in the given zone
     */
    public static function getInZone(Zone $zone){
        $table = self::getTable();
        $result = $table::getInZone($zone->zonex, $zone->zoney);
        $list = [];
        foreach($result as $row){
            $list[] = static::fromDbRow($row);
        }
        return $list;
    }

    protected static function getTable(){
        return self::$typeToTable[static::$type];
    }

    protected static function audiosFromDbRow($row){
        $audios = [];
        foreach($row['audios'] as $num => $a){
            $audios[] = new AudioInfo($num, $a['url'], $a['length']);
        }
        return $audios;
    }
}
?>
