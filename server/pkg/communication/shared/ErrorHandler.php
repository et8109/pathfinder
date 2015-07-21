<?php

/**
 * Handles errors in the communication layer
 */
class ErrorHandler{

    public static function handle($e){
        echo  json_encode(array(
            "error" => $e->getMessage()
        ));
    }
}

?>
