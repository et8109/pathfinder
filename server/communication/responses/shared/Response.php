<?php

class Response{

    protected $response;

    public function send(){
        return json_encode($this->response);
    }

}

?>
