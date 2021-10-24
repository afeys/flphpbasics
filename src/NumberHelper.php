<?php

namespace FL;

class NumberHelper {

    private $value;
    private $upperlimit = null; // if this is set, this is the maximum number that will be returned by the getValue() function
    private $lowerlimit = null; // if this is set, this is the minimum number that will be returned by the getValue() function

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
     * Initializes a NumberHelper instance. 
     * @param mixed $value  value to process, will be cast to a string first
     */
    function __construct($value = "") {
        $this->setValue($value);
    }
 
    /**
     * Returns the current value of the instance
     * 
     * @return string The current value of the NumberHelper instance
     */
    function __toString() {
        return $this->getValue();
    }

    // --------------------------------------------------------------------------------------//
    // SETTER FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     *  Sets the value to be used
     *  @param mixed $value  value to process
     * * @return object the NumberHelper instance
     */
    public function setValue($value) {
        if ($this->lowerlimit !== null) {
            if ($value < $this->lowerlimit) {
                $value = $this->lowerlimit;
            }
        }
        if ($this->upperlimit !== null) {
            if ($value > $this->upperlimit) {
                $value = $this->upperlimit;
            }
        }

        $this->value = $value;
        return $this;
    }

    /**
     * Sets the lowerlimit to be used in certain functions
     * @param mixed $value if the value is numeric lowerlimit will be set to value, if value is null, then lowerlimit will be cleared. All other values are disregarded
     * @return object the NumberHelper instance
     */
    public function setLowerLimit($value = null) {
        if (is_numeric($value)) {
            $this->lowerlimit = $value;
        } else {
            if ($value == null) {
                $this->lowerlimit = null;
            }
        }
        return $this;
    }

    /**
     * Sets the upperlimit to be used in certain functions
     * @param mixed $value if the value is numeric upperlimit will be set to value, if value is null, then upperlimit will be cleared. All other values are disregarded
     * @return object the NumberHelper instance
     */
    public function setUpperLimit($value = null) {
        if (is_numeric($value)) {
            $this->upperlimit = $value;
        } else {
            if ($value == null) {
                $this->upperlimit = null;
            }
        }
        return $this;
    }

    // --------------------------------------------------------------------------------------//
    // GETTER FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     * Returns the current value of the instance. 
     * 
     * @return string The current value of the NumberHelper instance
     */

    public function getValue() {
        if ($this->lowerlimit !== null) {
            if ($this->value < $this->lowerlimit) {
                $this->value = $this->lowerlimit;
            }
        }
        if ($this->upperlimit !== null) {
            if ($this->value > $this->upperlimit) {
                $this->value = $this->upperlimit;
            }
        }
        return $this->value;
    }

    /**
     *  Returns the lowerlimit
     * 
     * @return string The current lower limit
     */
    public function getLowerLimit() {
        return $this->lowerlimit;
    }

    /**
     *  Returns the upperlimit
     * 
     * @return string The current upper limit
     */
    public function getUpperLimit() {
        return $this->upperlimit;
    }

    // --------------------------------------------------------------------------------------//
    // MODIFIER FUNCTIONS                                                                    //
    // --------------------------------------------------------------------------------------//

    /**
     * This function randomizes the value of the NumberHelper instance
     * It will use the lowerlimit and upperlimit set with the setLowerLimit and setUpperLimit functions
     * or default it will use a lowerlimit of 0 and an upperlimit of 100
     * @return object the NumberHelper instance
     */
    public function randomize() {
        $bottomvalue = $this->getLowerLimit();
        if ($bottomvalue == null) {
            $bottomvalue = 0;
        }
        $topvalue = $this->getUpperLimit();
        if ($topvalue == null) {
            $topvalue = 100;
        }
        srand((double) microtime() * 1000000);
        // take in to account that user might enter the limits wrong
        if ($topvalue < $bottomvalue) {
            $this->setValue(rand($topvalue, $bottomvalue));
        } else {
            $this->setValue(rand($bottomvalue, $topvalue));
        }
        return $this;
    }

    /**
     * Rounds the value to the specified precision
     * @param integer $precision = number of decimals
     * @param string $mode =  PHP_ROUND_HALF_UP, PHP_ROUND_HALF_DOWN, PHP_ROUND_HALF_EVEN, or PHP_ROUND_HALF_ODD.
     * @return object the NumberHelper instance
     */
    public function round($precision = 0, $mode = PHP_ROUND_HALF_UP) {
        $this->setValue(round($this->getValue(), $precision, $mode));
        return $this;
    }

    /**
     * this flips the value between lowerlimit and upperlimit.
     * if lowerbound = 9 and upperbound = 20, then if value = 9 it becomes 20, if value = 10 it becomes 19, ....
     * @return object the NumberHelper instance
     */
    public function flip() {
        $bottomvalue = $this->getLowerLimit();
        if ($bottomvalue == null) {
            $bottomvalue = 0;
        }
        $topvalue = $this->getUpperLimit();
        if ($topvalue == null) {
            $topvalue = 100;
        }
        if ($topvalue < $bottomvalue) {
            $this->setValue($this->lowerlimit - ($this->value - $this->upperlimit));
        } else {
            $this->setValue($this->upperlimit - ($this->value - $this->lowerlimit));
        }
        return $this;
    }

    // --------------------------------------------------------------------------------------//
    // CHECKER FUNCTIONS                                                                     //
    // --------------------------------------------------------------------------------------//

    /**
     * Checks whether value is odd
     * @return boolean true or false
     */
    public function isOdd() {
        return (is_numeric($this->getValue()) & ($this->getValue() & 1));
    }

    /**
     * Checks whether value is even
     * @return boolean true or false
     */
    public function isEven() {
        return (is_numeric($this->getValue()) & (!($this->getValue() & 1)));
    }
}