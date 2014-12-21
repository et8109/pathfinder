<?php
class ErrorHandler{
    public function __construct() {}// static only
    
    public static function handle($e){
        echo $e->getMessage();
    }
}
?>