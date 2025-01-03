<?php

namespace FL;

class ObjectHelper {

    private $object = null;

    /**
     * Helper function to the constructor.
     * This allows chaining multiple commands in one line:
     * $number = NumberHelper::getInstance(100500)->setLowerLimit(10)->setUpperLimit(20)->randomize()->getValue();
     * getInstance takes the exact same parameters as the __construct method.
     * @param mixed $value  value to process, will be cast to a string first
     * @return object the NumberHelper instance
     */
    public static function getInstance($value = "") {
        $class = __CLASS__;
        return new $class($value);
    }

    // --------------------------------------------------------------------------------------//
    // __ FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     * Initializes an ObjectHelper instance.
     * @param object $object
     */
    function __construct($object) {
        if (is_object($object)) {
            $this->object = $object;
        }
    }

    function getItemWithKey($key, $default = null) {
        if ($this->object !== null) {
            if (property_exists($this->object, $key)) {
                return $this->object->$key;
            }
        }
        return $default;
    }

}