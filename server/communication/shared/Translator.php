<?php
/*
 * translates to and from json in the communication layer
 */
class Translator{

    private static $time;//server time when initialized
    private static $JSON;
    private static $initialized = false;

    protected function __construct(){}//static only

    public static function init(){
        if(self::$initialized){
            throw new Exception("Translator initialized twice");
        }
        //initialize
        self::$time = time();
        self:$JSON = array();
    }

    public static function add($arr){
        self::$JSON[] = $arr;
    }

    public static function send(){
        return json_encode(self::$JSON);
    }

    public static function clear(){
        self::$JSON = array();
    }
}
Translator::init();
?>
