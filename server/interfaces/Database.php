<?php

class Database{

    protected function __construct(){}//static only

        public static function resetdb(){
            require_once(constants::server_root."/database/core/database.php");
            DBCore::resetdb();
    }
}
?>
