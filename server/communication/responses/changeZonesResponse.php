<?php
require_once("shared/Response.php");

class ChangeZoneResponse extends Response{

    public function __construct(){
        $this->response = array(
            "prep" => array(
                "endAmb" => array(),
                "npcs" => array(),
                "enemies" => array(),
                "amb" => array()
            ),
            "play" => array(
                "npcs" => array(),
                "enemies" => array(),
                "ambients" => array(),
                "player" => array(),
                "sprite" => array()
            )
        );
    } 

    public function add_prep_endAmb($data){
        $this->response['prep']['endAmb'][] = $data;
    }
    public function add_prep_npcs($data){
        $this->response['prep']['npcs'][] = $data;
    }
    public function add_prep_enemies($data){
        $this->response['prep']['enemies'][] = $data;
    }
    public function add_prep_amb($data){
        $this->response['prep']['amb'][] = $data;
    }

    public function add_play_npcs($data){
        $this->response['play']['npcs'][] = $data;
    }
    public function add_play_enemies($data){
        $this->response['play']['enemies'][] = $data;
    }
    public function add_play_ambients($data){
        $this->response['play']['ambients'][] = $data;
    }
    public function add_play_player($data){
        $this->response['play']['player'][] = $data;
    }
    public function add_play_sprite($data){
        $this->response['play']['sprite'][] = $data;
    }
}
?>
