<?php

namespace FL;

// TODO : is preg_replace and str_replace mb aware ?
// TODO : add a function regexp($regexp) to allow regexp modifications on the value string

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use InvalidArgumentException;
use OutOfBoundsException;

class StringHelper implements IteratorAggregate, ArrayAccess, Countable {

    // constants used as parameters for some functions
    const ALL = "All";
    const FIRST = "First";
    const LAST = "Last";

    /**
     * The stringvalue of an instance of StringHelper
     * @var string
     */
    private $value = "";

    /**
     * The encoding used for the string stored in $value. This should
     * be one of the encodings supported by the mbstring module
     */
    private $encoding = "";

    /**
     * Helper function to the constructor.
     * This allows chaining multiple commands in one line:
     * $colorarray = StringHelper::getInstance("Blue Yellow Green Red")->toLower()->toArray(" ");
     * 
     * getInstance takes the exact same parameters as the __construct method.
     *
     * @param mixed $value  value to process, will be cast to a string first
     * @param string $encoding  the character encoding
     * 
     * @throws \InvalidArgumentException when the first argument is an object without a __toString method
     * 
     * @return object the Stringhelper instance
     */
    public static function getInstance($value = "", $encoding = null) {
        $class = __CLASS__;
        return new $class($value, $encoding);
    }

    // --------------------------------------------------------------------------------------//
    // __ FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     * Initializes a StringHelper instance. 
     * if you pass an object to the constructor function, this will be fine
     * if the object implements a __toString method. 
     * if you pass an array to the getInstance function, then the array will be imploded
     * but no separator will be used. If you want to use a separator, please use the
     * fromArray($array, $separator) function.
     * 
     * @param mixed $value  value to process, will be cast to a string first
     * @param string $encoding  the character encoding
     * @throws \InvalidArgumentException when the first argument is an object without a __toString method
     */
    function __construct($value = "", $encoding = "") {
        $this->setValue($value);
        $this->setEncoding($encoding);
    }

    /**
     * Returns the current value of the instance
     * 
     * @return string The current value of the StringHelper instance
     */
    function __toString() {
        return $this->toString();
    }

    // --------------------------------------------------------------------------------------//
    // FUNCTIONS NEEDED FOR INTERFACES                                                       //
    // --------------------------------------------------------------------------------------//

    /**
     * Returns a new ArrayIterator, this is necessary for the implementation of the IteratorAggregate
     * interface. The constructor for the ArrayIterator gets passed an array of characters in the
     * $value string. That way the use of foreach is possible
     */
    public function getIterator(): \Traversable {
        return new ArrayIterator($this->getValueAsCharArray());
    }

    /**
     * Returns the number of characters in the value of the StringHelper instance. This function
     * is necessary for the implementation of the Countable interface
     */
    public function count(): int {
        return $this->getLength();
    }

    /**
     * The following offsetXXXXXXXX functions are necessary for the implementation of the ArrayAccess interface 
     * */
    public function offsetExists($offset) {
        $length = $this->getLength();
        $offset = (int) $offset;
        if ($offset >= 0) {
            return ($length > $offset);
        }
        return ($length >= abs($offset));
    }

    public function offsetGet($offset) {
        $length = $this->getLength();
        $offset = (int) $offset;
        if (($offset >= 0 && $length <= $offset) || $length < abs($offset)) {
            throw new OutOfBoundsException("There is no character at this index");
        }
        return \mb_substr($this->getValue(), $offset, 1, $this->encoding);
    }

    public function offsetSet($offset, $value) {
        $length = $this->getLength();
        $offset = (int) $offset;
        if (($offset >= 0 && $length <= $offset) || $length < abs($offset)) {
            throw new OutOfBoundsException("There is no character at this index");
        }
        $this->setCharacterAt($offset, $value);
    }

    public function offsetUnset($offset): void {
        $length = $this->getLength();
        $offset = (int) $offset;
        if (($offset >= 0 && $length <= $offset) || $length < abs($offset)) {
            throw new OutOfBoundsException("There is no character at this index");
        }
        $this->removeCharacterAt($offset);
    }

    // --------------------------------------------------------------------------------------//
    // SETTER FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     *  Sets the value to be used
     *  @param mixed $value  value to process, will be cast to a string first
     *  @throws \InvalidArgumentException when the argument is an object without a __toString method
     *      
     * * @return object the Stringhelper instance
     */
    public function setValue($value) {
        if (is_array($value)) {
            $value = implode(" ", $value);
        } else {
            if (is_object($value) && !method_exists($value, '__toString')) {
                throw new InvalidArgumentException('Object does not implement __toString');
            }
        }
        $this->value = (string) $value;
        return $this;
    }

    /**
     * Implodes an array using the separator and sets this as the value of
     * the StringHelper instance
     * @param string $separator character or string to use as separator between two array members
     * @param array $array array to implode and use as value
     * 
     * @return object the Stringhelper instance
     */
    public function fromArray($separator, $array) {
        $this->setValue(implode($separator, $array));
        return $this;
    }

    /**
     * sets the encoding used for the value string
     * @param string $encoding encoding used for the value string, otherwise use default mb internal encoding
     * 
     * @return object the Stringhelper instance
     */
    public function setEncoding($encoding = "") {
        $this->encoding = $encoding ?: \mb_internal_encoding();
        return $this;
    }

    // --------------------------------------------------------------------------------------//
    // GETTER FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     * Returns the current value of the instance. This function is only used
     * internally in StringHelper, therefor it is a private function
     * 
     * @return string The current value of the StringHelper instance
     */
    private function getValue() {
        return $this->value;
    }

    /**
     * Returns the current string value of the instance. 
     * 
     * @return string The current value of the StringHelper instance
     */
    public function toString() {
        return $this->value;
    }

    /**
     * Returns the current value of the instance as an array.
     * The value is split into parts based on the value of $separator
     * 
     * @param string $separator split the value at these separators
     * 
     * @return array containing the value parts
     */
    public function toArray($separator) {
        return explode($separator, $this->toString());
    }

    /**
     * Returns the current value of the instance as an ArrayHelper instance.
     * The value is split into parts based on the value of $separator
     * 
     * @param string $separator split the value at these separators
     * 
     * @return ArrayHelper
     */
    public function toArrayHelper($separator) {
        return ArrayHelper::getInstance($this->toArray());
    }

    /**
     * Returns the current value of the instance as an array of words.
     * If words are contained between quotes, they are treated as one word
     * e.g.  'apple lime "yellow banana" pear' will return an array with the
     * following items
     * - apple
     * - lime
     * - "yellow banana"
     * - pear
     * 
     * @return array containing the words
     */
    public function sentenceToWordArray() {
        preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $this->toString(), $matches);
        return $matches;
    }
    /**
     * @param type $data
     * @return string json_encoded value
     */
    public function toJSON() {
        return json_encode(utf8_encode($this->getValue()));
    }

    /**
     * Returns the current encoding of the value string
     * 
     * @return string encoding of the string
     */
    public function getEncoding() {
        return $this->encoding;
    }

    /**
     * Returns the length of the string
     * 
     * @return integer length of the string
     */
    public function getLength() {
        return \mb_strlen($this->getValue(), $this->getEncoding());
    }

    /**
     * Returns a substring of the value string
     * @param integer $start (either a position number, or StringHelper::FIRST to start at first char)
     * @param integer $length (either a number indication the lenght of the substring, or StringHelper::ALL to take rest of value string)
     * 
     * @return string substring
     */
    public function getSubString($start = StringHelper::FIRST, $length = StringHelper::ALL) {
        if ($start == StringHelper::FIRST) {
            $start = 0;
        }
        if ($length == StringHelper::ALL) {
            $length = $this->getLength() - $start;
        }
        $length = $length === null ? $this->getLength() : $length;
        return \mb_substr($this->getValue(), $start, $length, $this->encoding);
        // return substr($this->toString(), $idxstart, $length);
    }

    /**
     * Returns the character at the $positionindex (starting at 0)
     * @param integer $positionindex position of the character to return
     * @return string character at the $positionindex
     */
    public function getCharacterAt($positionindex) {
        return $this->getSubString($positionindex, 1);
    }

    /**
     * Returns an array of all characters in the string
     * 
     * @return array An array of string characters
     */
    public function getValueAsCharArray() {
        $returnvalue = [];
        $length = $this->getLength();
        for ($i = 0; $i < $length; $i++) {
            $returnvalue[] = $this->getCharacterAt($i);
        }
        return $returnvalue;
    }

    /**
     * Counts the number of occurrences of string $substring in value
     * @param string $substring  string to search for
     */
    public function countOccurrences($substring) {
        return mb_substr_count($this->value, $substring, $this->getEncoding());
    }

    // --------------------------------------------------------------------------------------//
    // HELPER FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     * Replace all occurrences of the search string with the replacement string. Multibyte safe.
     *
     * @param string|array $search The value being searched for, otherwise known as the needle. An array may be used to designate multiple needles.
     * @param string|array $replace The replacement value that replaces found search values. An array may be used to designate multiple replacements.
     * @param string|array $subject The string or array being searched and replaced on, otherwise known as the haystack.
     *                              If subject is an array, then the search and replace is performed with every entry of subject, and the return value is an array as well.
     * @param string $encoding The encoding parameter is the character encoding. If it is omitted, the internal character encoding value will be used.
     * @param int $count If passed, this will be set to the number of replacements performed.
     * @return array|string
     */
    public static function mb_str_replace($search, $replace, $subject, $encoding = 'auto', &$count = 0) {
        if (!is_array($subject)) {
            $searches = is_array($search) ? array_values($search) : [$search];
            $replacements = is_array($replace) ? array_values($replace) : [$replace];
            $replacements = array_pad($replacements, count($searches), '');
            foreach ($searches as $key => $search) {
                $replace = $replacements[$key];
                $search_len = mb_strlen($search, $encoding);

                $sb = [];
                while (($offset = mb_strpos($subject, $search, 0, $encoding)) !== false) {
                    $sb[] = mb_substr($subject, 0, $offset, $encoding);
                    $subject = mb_substr($subject, $offset + $search_len, null, $encoding);
                    ++$count;
                }
                $sb[] = $subject;
                $subject = implode($replace, $sb);
            }
        } else {
            foreach ($subject as $key => $value) {
                $subject[$key] = self::mb_str_replace($search, $replace, $value, $encoding, $count);
            }
        }
        return $subject;
    }

    /**
     * @param mixed $string The input string.
     * @param mixed $replacement The replacement string.
     * @param mixed $start If start is positive, the replacing will begin at the start'th offset into string.  If start is negative, the replacing will begin at the start'th character from the end of string.
     * @param mixed $length If given and is positive, it represents the length of the portion of string which is to be replaced. If it is negative, it represents the number of characters from the end of string at which to stop replacing. If it is not given, then it will default to strlen( string ); i.e. end the replacing at the end of string. Of course, if length is zero then this function will have the effect of inserting replacement into string at the given start offset.
     * @return string The result string is returned. If string is an array then array is returned.
     */
    private function mb_substr_replace($string, $replacement, $start, $length = NULL) {
        if (is_array($string)) {
            $num = count($string);
            // $replacement
            $replacement = is_array($replacement) ? array_slice($replacement, 0, $num) : array_pad(array($replacement), $num, $replacement);
            // $start
            if (is_array($start)) {
                $start = array_slice($start, 0, $num);
                foreach ($start as $key => $value)
                    $start[$key] = is_int($value) ? $value : 0;
            } else {
                $start = array_pad(array($start), $num, $start);
            }
            // $length
            if (!isset($length)) {
                $length = array_fill(0, $num, 0);
            } elseif (is_array($length)) {
                $length = array_slice($length, 0, $num);
                foreach ($length as $key => $value)
                    $length[$key] = isset($value) ? (is_int($value) ? $value : $num) : 0;
            } else {
                $length = array_pad(array($length), $num, $length);
            }
            // Recursive call
            return array_map(__FUNCTION__, $string, $replacement, $start, $length);
        }
        preg_match_all('/./us', (string) $string, $smatches);
        preg_match_all('/./us', (string) $replacement, $rmatches);
        if ($length === NULL)
            $length = mb_strlen($string);
        array_splice($smatches[0], $start, $length, $rmatches[0]);
        return join($smatches[0]);
    }

    // --------------------------------------------------------------------------------------//
    // MODIFIER FUNCTIONS                                                                    //
    // --------------------------------------------------------------------------------------//

    /**
     * This function randomizes the value of the StringHelper instance
     * @param int $minlength this is the minimumlenght of the randomized string
     * @param int $maxlength this is the maximumlength of the randomized string
     * @param int $booluselower indicates whether to use lower case letters
     * @param int $booluseupper indicates whether to use upper case letters
     * @param int $boolusespecial indicates whether to use special characters
     * @param int $boolusenumbers indicates whether to use numerics
     * 
     * @return object the StringHelper instance
     */
    public function randomize($minlength = 8, $maxlength = 8, $booluselower = true, $booluseupper = true, $boolusespecial = true, $boolusenumbers = true) {
        $charset = "";
        $key = "";
        if ($booluselower) {
            $charset .= "abcdefghijklmnopqrstuvwxyz";
        }
        if ($booluseupper) {
            $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        if ($boolusenumbers) {
            $charset .= "0123456789";
        }
        if ($boolusespecial) {
            $charset .= "~@#$%^*()_+-={}|][";
        } // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";
        if ($charset == "") {
            // hey, if you don't want to use any charset, I'll just use them all!   :-P
            $charset .= "abcdefghijklmnopqrstuvwxyz";
            $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $charset .= "0123456789";
            $charset .= "~@#$%^*()_+-={}|][";
        }
        if ($minlength > $maxlength) {
            $length = mt_rand($maxlength, $minlength);
        } else {
            $length = mt_rand($minlength, $maxlength);
        }
        for ($i = 0; $i < $length; $i++) {
            $key .= $charset[(mt_rand(0, (mb_strlen($charset) - 1)))];
        }
        $this->setValue($key);
        return $this;
    }

    /**
     * Replaces the character at $positionindex with $char
     * 
     * @param integer $positionindex the position of the character to replace (0-based)
     * @param string $char the character to replace the current character with
     * @return object the Stringhelper instance
     * @throws \OutOfBoundsException if you try to replace a character outside the string
     */
    public function setCharacterAt($positionindex, $char) {
        if ($positionindex >= 0 && $positionindex <= $this->getLength()) {
            $this->setValue($this->mb_substr_replace($this->getValue(), $char, $positionindex, 1));
        } else {
            throw new OutOfBoundsException("Trying to access a character outside the string");
        }
        return $this;
    }

    /**
     * Removes the character at $positionindex
     * 
     * @param integer $positionindex the position of the character to remove (0-based)
     * @return object the Stringhelper instance
     * @throws \OutOfBoundsException if you try to remove a character outside the string
     */
    public function removeCharacterAt($positionindex) {
        if ($positionindex >= 0 && $positionindex <= $this->getLength() && $this->getLength() > 0) {
            if ($positionindex == 0) {
                $this->setValue(mb_substr($this->getValue(), 1, null, $this->getEncoding()));
            } else {
                if ($positionindex == $this->getLength()) {
                    $this->setValue(mb_substr($this->getValue(), 0, $this->getLength() - 1, $this->getEncoding()));
                } else {
                    $this->setValue(mb_substr($this->getValue(), 0, $positionindex, $this->getEncoding()) . mb_substr($this->getValue(), $positionindex + 1, null, $this->getEncoding()));
                }
            }
        } else {
            throw new OutOfBoundsException("Trying to access a character outside the string");
        }
        return $this;
    }

    /**
     * Inserts a character at $positionindex
     * 
     * @param integer $positionindex the position of the character to remove (0-based)
     * @param string $string the string to insert at the position
     * @return object the Stringhelper instance
     * @throws \OutOfBoundsException if you try to remove a character outside the string
     */
    public function insertAt($positionindex, $string) {
        if ($positionindex >= 0 && $positionindex <= $this->getLength() && $this->getLength() > 0) {
            if ($positionindex == 0) {
                $this->setValue($string . $this->getValue());
            } else {
                if ($positionindex == $this->getLength()) {
                    $this->setValue($this->getValue() . $string);
                } else {
                    $this->setValue(mb_substr($this->getValue(), 0, $positionindex, $this->getEncoding()) . $string . mb_substr($this->getValue(), $positionindex, null, $this->getEncoding()));
                }
            }
        } else {
            throw new OutOfBoundsException("Trying to access a character outside the string");
        }
        return $this;
    }

    /**
     * Appends a string to value
     * @param string $str  string to append
     * 
     * @return object the StringHelper instance
     */
    public function append($str) {
        $this->setValue($this->toString() . $str);
        return $this;
    }

    /**
     * Prepends a string to value
     * @param string $str  string to prepend
     * 
     * @return object the StringHelper instance
     */
    public function prepend($str) {
        $this->setValue($str . $this->toString());
        return $this;
    }

    /**
     * Sanitizes the string and removes all tags and htmlentities
     * 
     * @return object the StringHelper instance
     */
    public function sanitize() {
        $this->setValue(htmlentities(strip_tags($this->getValue())));
        return $this;
    }

    /**
     * Converts the string to lowercase
     * 
     * @return object the StringHelper instance
     */
    public function toLower() {
        $this->setValue(mb_strtolower($this->getValue()));
        return $this;
    }

    /**
     * Converts the string to uppercase
     * 
     * @return object the StringHelper instance
     */
    public function toUpper() {
        $this->setValue(mb_strtoupper($this->getValue()));
        return $this;
    }

    /**
     * This removes all non alphanumeric (a-z, A-Z, 0-9, and space) characters from the string
     * 
     * @return object the StringHelper instance
     */
    public function toAlphaNumeric() {
        $_newval = preg_replace("/[^A-Za-z0-9\s\s+]/", "", $this->getValue());
        $_newval = str_replace("\n", "", $_newval);
        $_newval = str_replace("\r", "", $_newval);
        $this->setValue($_newval);
        return $this;
    }

    /**
     * This removes all non numeric (0-9) characters from the string
     * 
     * @return object the StringHelper instance
     */
    public function toNumeric() {
        $_newval = preg_replace("/[^0-9\s\s+]/", "", $this->getValue());
        $_newval = str_replace(" ", "", $_newval);
        $_newval = str_replace("\n", "", $_newval);
        $_newval = str_replace("\r", "", $_newval);
        $this->setValue($_newval);
        return $this;
    }

    /**
     * This removes (ALL/FIRST/LAST = defined by $scope) occurences of $replacewhat with $replacewith
     * 
     * @return object the StringHelper instance
     */
    function replace($replacewhat, $replacewith, $scope = StringHelper::ALL) {
        if (!($replacewhat == "" || $replacewhat == null) && !is_array($replacewhat)) {
            if ($scope == StringHelper::ALL) {
                $this->setValue($this->mb_str_replace($replacewhat, $replacewith, $this->getValue(), $this->getEncoding()));
            }
            if ($scope == StringHelper::LAST || $scope == StringHelper::FIRST) {
                if ($scope == StringHelper::LAST) {
                    $pos = mb_strrpos($this->getValue(), $replacewhat);
                }
                if ($scope == StringHelper::FIRST) {
                    $pos = mb_strpos($this->getValue(), $replacewhat);
                }
                if ($pos !== false) {
                    $firstpart = "";
                    if ($pos > 0) {
                        $firstpart = $this->getSubString(0, $pos);
                    }
                    $endpart = $this->getSubString($pos + mb_strlen($replacewhat), StringHelper::ALL);
                    $this->setValue($firstpart . $replacewith . $endpart);
                }
            }
        }
        return $this;
    }

    /**
     * Removes excessive white space
     * @return object the Stringhelper instance
     */
    function removeExcessiveWhiteSpace() {
        $this->setValue(preg_replace('!\s+!', ' ', $this->getValue()));
        return $this;
    }

    /**
     * Removes $str from the beginning of value if it is present
     * @param string $str string to remove from beginning of the value of StringHelper
     * @return object the Stringhelper instance
     */
    public function removeFromStart($str) {
        if ($this->startsWith($str)) {
            $this->setValue(mb_substr($this->toString(), mb_strlen($str)));
        }
        return $this;
    }

    /**
     * Removes $str from the end of value if it is present
     * @param string $str string to remove from end of the value of StringHelper
     * @return object the Stringhelper instance
     */
    public function removeFromEnd($str) {
        if ($this->endsWith($str)) {
            $this->setValue(mb_substr($this->toString(), 0, (mb_strlen($str) * (-1))));
        }
        return $this;
    }

    /**
     * gets everything before the last occurrence of $str
     * @param string $str string to search for
     * @param boolean $return_all_if_not_found determines whether the value is set to null, or to the full string if nothing found
     * @return object the StringHelper instance
     */
    public function keepEverythingBeforeLast($str, $return_all_if_not_found = false) {
        if ($this->contains($str)) {
            $this->setValue(mb_substr($this->getValue(), 0, mb_strrpos($this->getValue(), $str)));
        } else {
            if ($return_all_if_not_found == true) {
                
            } else {
                $this->setValue(null);
            }
        }
        return $this;
    }

    /**
     * gets everything after the last occurrence of $str
     * @param string $str string to search for
     * @param boolean $return_all_if_not_found determines whether the value is set to null, or to the full string if nothing found
     * @return object the StringHelper instance
     */
    public function keepEverythingAfterLast($str, $return_all_if_not_found = false) {
        if ($this->contains($str)) {
            $this->setValue(mb_substr($this->getValue(), mb_strrpos($this->getValue(), $str) + mb_strlen($str)));
        } else {
            if ($return_all_if_not_found == true) {
                
            } else {
                $this->setValue(null);
            }
        }
        return $this;
    }

    /**
     * gets everything before the first occurrence of $str
     * @param string $str string to search for
     * @param boolean $return_all_if_not_found determines whether the value is set to null, or to the full string if nothing found
     * @return object the StringHelper instance
     */
    public function keepEverythingBeforeFirst($str, $return_all_if_not_found = false) {
        if ($this->contains($str)) {
            $this->setValue(mb_substr($this->getValue(), 0, mb_strpos($this->getValue(), $str)));
        } else {
            if ($return_all_if_not_found == true) {
                
            } else {
                $this->setValue(null);
            }
        }
        return $this;
    }

    /**
     * gets everything after the first occurrence of $str
     * @param string $str string to search for
     * @param boolean $return_all_if_not_found determines whether the value is set to null, or to the full string if nothing found
     * @return object the StringHelper instance
     */
    public function keepEverythingAfterFirst($str, $return_all_if_not_found = false) {
        if ($this->contains($str)) {
            $this->setValue(mb_substr($this->getValue(), mb_strpos($this->getValue(), $str) + mb_strlen($str)));
        } else {
            if ($return_all_if_not_found == true) {
                
            } else {
                $this->setValue(null);
            }
        }
        return $this;
    }

    /**
     * this flips all values in the string: a becomes z, b becomes y, ....
     * relevant ASCII values:   0 -> 9 (decimal 48 till 57)
     *                          A -> Z (decimal 65 till 90)
     *                          a -> z (decimal 97 till 122)
     * @return object the StringHelper instance
     */
    public function flip() {
        $newString = "";
        for ($i = 0; $i < mb_strlen($this->value); ++$i) {
            // ascii value
            $val = ord($this->value[$i]);
            $newval = $val;
            if ($val >= 48 && $val <= 57) {
                $newval = NumberHelper::getInstance($val)->setLowerLimit(48)->setUpperLimit(57)->flip()->getValue();
            }
            if ($val >= 65 && $val <= 90) {
                $newval = NumberHelper::getInstance($val)->setLowerLimit(65)->setUpperLimit(90)->flip()->getValue();
            }
            if ($val >= 97 && $val <= 122) {
                $newval = NumberHelper::getInstance($val)->setLowerLimit(97)->setUpperLimit(122)->flip()->getValue();
            }
            $newString = $newString . chr($newval);
        }
        $this->setValue($newString);
        return $this;
    }

    /**
     * this function will automatically append a number to the end of a string, or increment the number if it already was added
     * the value will be incremented with $addvalue
     * @param integer $addvalue = value to increment with
     * @param integer $startvalue = initial value if the string doesn't already end in a number
     * 
     * @return object the StringHelper instance
     */
    public function increment($addvalue = 1, $startvalue = 1) {
        // Step 1: check if string ends with a numeric value
        $_stringvalue = $this->getValue();
        $_numberpart = "";
        $_stringpart = "";
        for ($i = mb_strlen($_stringvalue); $i >= 0; $i--) {
            $char = mb_substr($_stringvalue, $i, 1);
            if ($char >= '0' && $char <= '9') {
                $_numberpart = $char . $_numberpart;
            } else {
                $_stringpart = $char . $_stringpart;
            }
        }
        if (mb_strlen($_numberpart) > 0) {
            // Step 2: strip numeric value from string
            // Step 3: increment numeric value
            $_numberpart = "" . (intval($_numberpart) + $addvalue);

            // Step 4: reappend numeric value to string
            $this->setValue($_stringpart . $_numberpart);
        } else {
            // if no number present, append the default
            $this->setValue($_stringpart . $startvalue);
        }
        // Step 5: return result;
        return $this;
    }

    // --------------------------------------------------------------------------------------//
    // CHECKER FUNCTIONS                                                                     //
    // --------------------------------------------------------------------------------------//

    /**
     * Checks whether value is equal to $str
     * @param string str value to compare with
     * 
     * @return boolean true or false
     */
    public function equals($str) {
        if ($this->toString() == $str) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks whether value is not equal to $str
     * @param string str value to compare with
     * 
     * @return boolean true or false
     */
    public function notEquals($str) {
        if ($this->equals($str)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Checks whether value is empty
     * @return boolean true or false
     */
    public function isEmpty() {
        if ($this->toString() === "" || $this->getValue() === null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks whether value is null
     * @return boolean true or false
     */
    public function isNull() {
        if ($this->getValue() == null) {
            return true;
        }
        return false;
    }

    /**
     * Checks whether value is equal to 0 (numeric zero)
     * @return boolean true or false
     */
    public function isZero() {
        if ($this->getValue() == "0") {
            return true;
        }
        return false;
    }

    /**
     * Checks whether value is null or zero
     * @return boolean true or false
     */
    public function isEmptyNullOrZero() {
        if ($this->isEmpty() || $this->isZero()) {
            return true;
        }
        return false;
    }

    /**
     * Checks whether value is not null and not empty
     * @return boolean true or false
     */
    public function isFilled() {
        return !$this->isEmpty();
    }

    /**
     * Checks whether value is in a provided array
     * @param array $valarray array containing the values to check against
     * @param boolean $casesensitive indicates if search has to be done case sensitive or insensitive
     * @return boolean true or false
     */
    public function inArray($valarray, $casesensitive = false) {
        foreach ($valarray as $val) {
            if ($casesensitive == true) {
                if ($val == $this->getValue()) {
                    return true;
                }
            } else {
                if (mb_strtoupper($val) == mb_strtoupper($this->getValue())) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Checks whether value starts with $str
     * @param string $str 
     * @return boolean true or false
     */
    public function startsWith($str) {
        $len = mb_strlen($str);
        $value_start = mb_substr($this->toString(), 0, $len);
        return $value_start == $str;
    }

    /**
     * Checks whether value ends with $str
     * @param string $str 
     * @return boolean true or false
     */
    public function endsWith($str) {
        $len = mb_strlen($str);
        $value_end = mb_substr($this->toString(), mb_strlen($this->toString()) - $len);
        return $value_end == $str;
    }

    /**
     * Checks whether value contains $str
     * @param string $str 
     * @return boolean true or false
     */
    public function contains($strOrArray, $and = true) {
        if (is_array($strOrArray)) {
            $defaultreturn = false;
            if ($and) {
                $defaultreturn = true;
            } else {
                $defaultreturn = false;
            }
            foreach ($strOrArray as $str) {
                if (!is_array($str)) {
                    $returnvalue = $this->contains($str);
                    if ($and) { // AND, if one not found, return false
                        if ($returnvalue == false) {
                            return false;
                        }
                    } else { // OR, if one found, return true;
                        if ($returnvalue == true) {
                            return true;
                        }
                    }
                }
            }
            return $defaultreturn;
        } else {
            $returnvalue = false;
            if (mb_strpos($this->toString(), $strOrArray) !== false) {
                $returnvalue = true;
            }
        }
        return $returnvalue;
    }

    /**
     * checks if the value is a valid url
     * 
     * @return boolean
     */
    public function isValidUrl() {
        if (filter_var($this->getValue(), FILTER_VALIDATE_URL)) {
            return true;
        }
        return false;
    }

    /**
     * checks if the value is a valid mailaddress
     * 
     * @return boolean
     */
    public function isValidMailAddress() {
        if (filter_var($this->getValue(), FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    /**
     * Checks if the string contains any multibyte characters
     * @return boolean true if the string contains any multibyte characters, false otherwise
     */
    public function containsMultibyteCharacters() {
        return !mb_check_encoding($this->getValue(), 'ASCII') && mb_check_encoding($this->getValue(), 'UTF-8');
    }

    // --------------------------------------------------------------------------------------//
    // CONDITIONAL RETURNS
    // --------------------------------------------------------------------------------------//

    /**
     * Checks if value is equal to $comparevalue, if it is, then return $returnvalueA, otherwise return $returnvalueB
     * @param string $comparevalue
     * @param string $returnvalueA
     * @param string $returnvalueB 
     * @return string depending on result of compare between value and $comparevalue
     */
    public function ifEqualThenElse($comparevalue, $returnvalueA, $returnvalueB) {
        if ($this->toString() == $comparevalue) {
            return $returnvalueA;
        } else {
            return $returnvalueB;
        }
    }

    /**
     * Checks if value is larger than $comparevalue, if it is, then return $returnvalueA, otherwise return $returnvalueB
     * @param string $comparevalue
     * @param string $returnvalueA
     * @param string $returnvalueB 
     * @return string depending on result of compare between value and $comparevalue
     */
    public function ifLargerThenElse($comparevalue, $returnvalueA, $returnvalueB) {
        if ($this->toString() > $comparevalue) {
            return $returnvalueA;
        } else {
            return $returnvalueB;
        }
    }

    /**
     * Checks if value is smaller than $comparevalue, if it is, then return $returnvalueA, otherwise return $returnvalueB
     * @param string $comparevalue
     * @param string $returnvalueA
     * @param string $returnvalueB 
     * @return string depending on result of compare between value and $comparevalue
     */
    public function ifSmallerThenElse($comparevalue, $returnvalueA, $returnvalueB) {
        if ($this->toString() < $comparevalue) {
            return $returnvalueA;
        } else {
            return $returnvalueB;
        }
    }

    /**
     * Checks if value is not empty, if it is, then return $str, otherwise return an empty string
     * @param string $str the value to return if value is not empty
     * @return string 
     */
    public function ifNotEmpty($str) {
        if ($this->isEmpty()) {
            return "";
        } else {
            return $str;
        }
    }

    /**
     * Checks if value is empty, if it is, then return $str, otherwise return an empty string
     * @param string $str the value to return if value is not empty
     * @return string 
     */
    public function ifEmpty($str) {
        if ($this->isEmpty()) {
            return $str;
        } else {
            return "";
        }
    }

}

/*


    const ALPHABETPARTALLNUM = "ALFANUMALL09";
    const ALPHABETPARTSPLITNUM = "ALFANNUMSPLIT09";
    
//WORK IN PROGRESS
    const TRUNCATEFIXEDCHARS = "TruncFixedChar";
    const TRUNCATEATWORDBREAK = "TruncWordBreak";
    const TRUNCATEATSENTENCEBREAK = "TruncSentenceBreak";
    const TRUNCATENUMBEROFWORDS = "TruncNumberOfWords";
    const TRUNCATENUMBEROFSENTENCES = "TruncNumberOfSentences";

    private $value = "";
    // only for alternation purposes:
    private $alternationvalues = array(true, false);
    private $alternationindex = 0;
    
    // alphabetindex stuff
    private $alphabetindexarray = array();
    private $alphabetindexpartsize = null;
    private $alphabetindexsplitnumbers = null;
    
    private $alphabet = array(   "0" => "A", "1" => "B", "2" => "C", "3" => "D", "4" => "E",
                                 "5" => "F", "6" => "G", "7" => "H", "8" => "I", "9" => "J", 
                                "10" => "K","11" => "L","12" => "M","13" => "N","14" => "O", 
                                "15" => "P","16" => "Q","17" => "R","18" => "S","19" => "T", 
                                "20" => "U","21" => "V","22" => "W","23" => "X","24" => "Y",
                                "25" => "Z" );





    public function toBeautify() {
        return strtoupper($this->getSubString(StringHelper::FIRST, 1)) . strtolower($this->getSubString(2));
    }

    public function getFieldX($fieldnum, $separator) {
        $_tmpArr = explode($separator, $this->toString());
        return $_tmpArr[$fieldnum];
    }


    public function setAlternationValues($valuearray) {
        if (is_array($valuearray)) {
            $this->alternationvalues = $valuearray;
        } else {
            throw new StringAlternationException("Wrong parameter to function FL\StringHelper::setAlternationValues. Parameter should be array.");
        }
        return $this;
    }

    public function alternate($increase_index = true) {
        $returnvalue = $this->alternationvalues[$this->alternationindex];
        if ($increase_index) {
            $this->alternationindex++;
            if ($this->alternationindex >= count($this->alternationvalues)) {
                $this->alternationindex = 0;
            }
        }
        return $returnvalue;
    }

    public function random($length = 16) {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        $this->setValue($string);
        return $this;
    }

  
    public function replace_accents() {
        $tmpstr = htmlentities($this->getValue(), ENT_COMPAT, "UTF-8");
        $tmpstr = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde);/','$1',$tmpstr);
        $this->setValue(html_entity_decode($tmpstr));
        return $this;
    }
    
    // this takes the first character of the value, and checks in which part of the alfabet it is located
    // it returns (for partsize 2): A-B, C-D, E-F, G-H, ....
    // for partsize 3 it would return A-C, D-F, G-I,...
    // if the first char is numeric, then it would return 0-9 if $splitnumbers = StringHelper::ALFABETPARTALLNUM
    // if it is Stringtool::ALFABETSPLITNUM, then it would return 0-1, 2-3, ... 
    public function getAlphabetPart($partsize = 2, $splitnumbers = StringHelper::ALPHABETPARTALLNUM) {
        // first fill the lookuptables if needed
        if ($this->alphabetindexpartsize !== $partsize || $this->alphabetindexsplitnumbers !== $splitnumbers) {
            // first the numbers
            for($i=0;$i<10;$i++) {
                $idxstr = "0-9";
                if ($splitnumbers == StringHelper::ALPHABETPARTSPLITNUM) {
                    $idx = floor($i / $partsize);
                    $idxstr = "" . ($idx * $partsize) . "-" . (($idx * $partsize) + $partsize - 1);
                }
                $this->alphabetindexarray[$i . ""] = $idxstr;
            }
            // then the characters
            for($i=0;$i<26;$i++) {
                $idxstr = "A-Z";
                $idx = floor($i / $partsize);
                $idxstr = $this->alphabet[($idx *$partsize)] . "-";
                if (array_key_exists("" . (($idx * $partsize) + $partsize - 1), $this->alphabet)) {
                            $idxstr .= $this->alphabet[($idx * $partsize) + $partsize - 1];
                } else {
                    $idxstr = $this->alphabet[count($this->alphabet) - 1];
                }
                $this->alphabetindexarray[strtoupper($this->alphabet[$i])] = $idxstr;
            }
            $this->alphabetindexpartsize = $partsize;
            $this->alphabetindexsplitnumbers = $splitnumbers;
        }
        // end of lookuptable fill

        $returnvalue = "";
        $firstchar = StringHelper::getInstance($this->getValue())->getSubString(StringHelper::FIRST, 1);
        $this->setValue($this->alphabetindexarray[strtoupper($firstchar)]);
        return $this;
    }
 


    public function htmlToPlainText() {
        $this->setValue(preg_replace("/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags($this->getValue())))));
        return $this;
    }


    public function appendWithIteration($count, $str) {
        for ($i = 0; $i < $count; $i++) {
            $this->setValue($this->toString() . $str);
        }
        return $this;
    }

    public function prependWithIteration($count, $str) {
        for ($i = 0; $i < $count; $i++) {
            $this->setValue($str . $this->toString());
        }
        return $this;
    }


    public function getLast($num) {
        $this->setValue($this->getSubString(strlen($this->toString()) - $num, $num));
        return $this;
    }

    public function getFirst($num, $adddots = false) {
        $oldvalue = $this->getValue();
        $this->setValue($this->getSubString(StringHelper::FIRST, $num));
        if ($adddots == true) {
            if ($oldvalue !== $this->getValue()) {
                $this->setValue($this->getValue() . "...");
            }
        }
        return $this;
    }

 

*/