<?php

/**
 * Handles errors in the communication layer
 */
class ErrorHandler{

    public static function handle($e){
        return json_encode(array(
            "error" => $e->getMessage()
        ));
    }
}

?>
