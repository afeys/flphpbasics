<?php

namespace FL;

class UrlHelper {
    /**
     * Helper function to the constructor.
     * This allows chaining multiple commands in one line:
     * $baseUrl = UrlHelper::getInstance()->getCurrentUrl()->addParameter("view", "home")->toUrl();
     * getInstance takes the exact same parameters as the __construct method.
     * @param mixed $value  value to process, will be cast to a string first
     * @return object the NumberHelper instance
     */
    public static function getInstance() {
        $class = __CLASS__;
        return new $class($value);
    }

    // --------------------------------------------------------------------------------------//
    // __ FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     * Initializes a UrlHelper instance. 
     */
    function __construct() {
    }
 
    
    function getBaseUrl(){
        if(isset($_SERVER['HTTPS'])){
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        }
        else{
            $protocol = 'http';
        }
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}
