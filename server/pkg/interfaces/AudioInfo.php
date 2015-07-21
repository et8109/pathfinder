<?php

class AudioInfo{

    public $url;
    public $length;
    public $num;

    public function __construct($num, $url, $length){
        $this->url = $url;
        $this->length = $length;
        $this->num = $num;
    }

}
?>
