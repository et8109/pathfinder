<?php
require_once("shared/Response.php");

class SetupResponse extends Response{

    public function __construct(){
        $this->response = array(
        );
    } 

    public function add_playerID($data){
        $this->response['playerID'] = $data;
    }
    public function add_playeraudioURL($data){
        $this->response['playeraudioURL'] = $data;
    }
    public function add_peerID($data){
        $this->response['peerID'] = $data;
    }
    public function add_zoneX($data){
        $this->response['zoneX'] = $data;
    }
    public function add_zoneY($data){
        $this->response['zoneY'] = $data;
    }
    public function add_version($data){
        $this->response['version'] = $data;
    }
}
?>
