<?php
/**
 * A class used to build front end page pages
 */

class PageBuilder {

    private $pageType;

    public __construct($pageType){
        $this->pageType = $pageType;
    }

    /**
     * Redirects the user to the index page if they are logged in.
     */
    public function redirectIfLogggedIn(){
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
            case pageTypes::normal:
                include("header.inc");
                break;
            case pageTypes::index:
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

    /**
     * Types of pages.
     */
    public static class pageTypes {
        const normal = 0;
        const index = 1;
        const admin = 2;
    }
}

?>
