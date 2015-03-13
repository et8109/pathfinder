<?php
/*
 * translates to and from json in the communication layer
 */
public class Translator{

    private static $time;//server time when initialized
    private static $JSON;
    private boolean $initialized = false;

    private function __construct(){}//static only

    public function init(){
        if(self::$initialized){
            throw new Exception("Translator initialized twice");
        }
        //initialize
        self::$time = time();
        self:$JSON = array();
    }
}

?>
