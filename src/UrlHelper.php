<?php

namespace FL;

class UrlHelper {
    /**
     * Helper function to the constructor.
     * This allows chaining multiple commands in one line:
     * $baseUrl = UrlHelper::getInstance()->getCurrentUrl()->addParameter("view", "home")->toUrl();
     * getInstance takes the exact same parameters as the __construct method.
     * @param mixed $value  value to process, will be cast to a string first
     * @return object the UrlHelper instance
     */
    public static function getInstance() {
        $class = __CLASS__;
        return new $class();
    }

    // --------------------------------------------------------------------------------------//
    // __ FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     * Initializes a UrlHelper instance. 
     */
    function __construct() {
    }
 
    function getProtocol() {
        if(isset($_SERVER['HTTPS'])){
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        }
        else{
            $protocol = 'http';
        }
        return $protocol;
    }
    
    function getHost() {
        return $_SERVER['HTTP_HOST'];
    }
    
    function getPath() {
        return $_SERVER['REQUEST_URI'];
    }
    
    function getBaseUrl(){
        return $this->getProtocol() . "://" . $this->getHost();
    }
}
