<?php
require_once("shared/Response.php");

class SetupResponse extends Response{

    public function __construct(){
        $this->response = array(
        );
    } 

    public function add_key($data){
        $this->response['key'] = $data;
    }
    public function add_playeraudioURL($data){
        $this->response['playeraudioURL'] = $data;
    }
    public function add_peerID($data){
        $this->response['peerID'] = $data;
    }
    public function add_version($data){
        $this->response['version'] = $data;
    }
}
?>
