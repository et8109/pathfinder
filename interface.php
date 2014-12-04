<?php
/**
 *The interface between the logic/application and the database
 */
public class Interface {
    
    protected function prepVar($var){
        $var = mysqli_real_escape_string($this->con,$var);
        //replace ' with ''
        //$var = str_replace("'", "''", $var);
        //if not a number, surround in quotes
        if(!is_numeric($var)){
            $var = "'".$var."'";
        }
        return $var;
    }
}
?>