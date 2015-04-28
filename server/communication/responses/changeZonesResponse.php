<?php
require_once("shared/Response.php");

class ChangeZoneResponse extends Response{

    public function __construct(){
        $this->response = array(
            "end" => array(),
            "prep" => array(),
            "play" => array()
        );
    } 

    public function end_amb($keyid){
        $this->response['end'][] = array(
            "key" => $keyid
        );
    }

    public function add_prep($keyid, $urls, $loop){
        $this->response['prep'][] = array(
            "key" => $keyid,
            "urls" => $urls,
            "loop" => $loop
        );
    }

    public function add_play($keyid, $num, $time, $dirx, $diry){
        $this->response['play'][] = array(
            "key" => $keyid,
            "num" => $num,
            "time" => $time,
            "dirx" => $dirx,
            "diry" => $diry
        );
    }
}
?>
