<?php

/**
 * Handles errors in the communication layer
 */
public class ErrorHandler{


    public static function handle($e){
        Translator::clear();
        Translator::add(array(
            "error" => ($e->getMessage())
        ));
        return Translator::send();
    }
}

?>
