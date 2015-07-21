<?php
require_once("shared/Response.php");

class SetupResponse extends Response{

    public function __construct(){
        $this->response = array(
        );
    } 

    public function add_prep($keyid, $urls, $loop){
        $this->response['prep'][] = array(
            "key" => $keyid,
            "urls" => $urls,
            "loop" => $loop
        );
    }

    public function add_peerID($data){
        $this->response['peerID'] = $data;
    }
    public function add_version($data){
        $this->response['version'] = $data;
    }
}
?>
