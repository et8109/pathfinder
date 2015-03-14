<?php
/**
 * A class used to build front end page pages
 */

class PageBuilder {

    const TYPE_NORMAL = 0;
    const TYPE_INDEX = 1;
    const TYPE_ADMIN = 2;

    private $pageType;

    public function __construct($pageType){
        $this->pageType = $pageType;
    }

    /**
     * Redirects the user to the index page if they are logged in.
     */
    public function redirectIfLoggedIn(){
        if(isset($_SESSION['playerID'])){
                header("Location: index.php");
        }
    }

    /**
     * Redirects the user to the login page if they are logged in.
     */
    public function redirectIfLoggedOut(){
        if(!isset($_SESSION['playerID'])){
                header("Location: login.php");
        }
    }

    /**
     * Adds a header to the top of the page
     */
    public function addHeader(){
        switch($this->pageType){
            case self::TYPE_NORMAL:
                include("header.inc");
                break;
            case self::TYPE_INDEX:
                include("headerIndex.inc");
                break;
        }
    } 

    /**
     *Adds a footer to the bottom of the page.
     */
    public function addFooter(){
        include("footer.inc");
    }
}



?>
