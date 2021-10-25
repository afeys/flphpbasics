<?php

namespace FL;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use InvalidArgumentException;
use OutOfBoundsException;

class ArrayHelper implements IteratorAggregate, ArrayAccess, Countable {

    private $value;

    /**
     * Helper function to the constructor.
     * This allows chaining multiple commands in one line
     * getInstance takes the exact same parameters as the __construct method.
     * @param mixed $value  array to process, has to be a PHP array
     * @return object the ArrayHelper instance
     */
    public static function getInstance($value = "") {
        $class = __CLASS__;
        return new $class($value);
    }

    // --------------------------------------------------------------------------------------//
    // __ FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     * Initializes a ArrayHelper instance. 
     * @param mixed $value  value to process, will be cast to a string first
     */
    function __construct($value = "") {
        $this->setValue($value);
    }
 
    /**
     * Returns the current value of the instance
     * 
     * @return string The current value of the ArrayHelper instance (imploded array)
     */
    function __toString() {
        return implode(" ", $this->getValue());
    }

    /**
     * Just to be complete, since there is a fromString() function, it might be
     * more logical if there is also a toString() function;
     * 
     * @return string The current value of the ArrayHelper instance (imploded array)
     */
    function toString() {
        return $this;
    }

    // --------------------------------------------------------------------------------------//
    // FUNCTIONS NEEDED FOR INTERFACES                                                       //
    // --------------------------------------------------------------------------------------//

    /**
     * Returns a new ArrayIterator, this is necessary for the implementation of the IteratorAggregate
     * interface. The constructor for the ArrayIterator gets passed the array value of ArrayHelper
     * That way the use of foreach is possible
     */

    public function getIterator(): \Traversable {
        return new ArrayIterator($this->getValue());
    }

    /**
     * Returns the number of items in the value array of the StringHelper instance. This function
     * is necessary for the implementation of the Countable interface
     */

    public function count(): int {
        return $this->getNumberOfArrayItems();
    }

    /** 
     * The following offsetXXXXXXXX functions are necessary for the implementation of the ArrayAccess interface 
     * */

    public function offsetExists( $offset) {
        $length = $this->count();
        $offset = (int) $offset;
        if ($offset >= 0) {
            return ($length > $offset);
        }        
        return ($length >= abs($offset));
    }

    public function offsetGet( $offset) {
        $length = $this->count();
        $offset = (int) $offset;
        if (($offset >= 0 && $length <= $offset) || $length < abs($offset)) {
            throw new OutOfBoundsException("There is no character at this index");
        }
        return $this->value[$offset];
    }

    public function offsetSet( $offset,  $value) {
        $length = $this->count();
        $offset = (int) $offset;
        if (($offset >= 0 && $length <= $offset) || $length < abs($offset)) {
            throw new OutOfBoundsException("There is no character at this index");
        }
        $this->value[$offset] = $value;
    }

    public function offsetUnset( $offset): void {
        $length = $this->count();
        $offset = (int) $offset;
        if (($offset >= 0 && $length <= $offset) || $length < abs($offset)) {
            throw new OutOfBoundsException("There is no character at this index");
        }
        unset($this->value[$offset]);
    }

    // --------------------------------------------------------------------------------------//
    // SETTER FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     *  Sets the value to be used
     *  @param mixed $value  value to process, has to be a PHP array
     * * @return object the ArrayHelper instance
     */
    public function setValue($value) {
        if (is_array($value)) {
            $this->value = $value;
        }
        return $this;
    }

    /**
     * Converts a string to an array (splitting the string wherever there is a $separator)
     * @param string $separator separator between two elements
     * @param string $string string containing the value to convert
     * 
     * @return object the ArrayHelper instance
     */
    public function fromString($separator, $string) {
        $this->value = explode($separator, $string);
        return $this;
    }

 
    // --------------------------------------------------------------------------------------//
    // GETTER FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     * Returns the current value of the instance. 
     * 
     * @return array The current value of the ArrayHelper instance
     */

    public function getValue() {
        return $this->value;
    }

    /**
     * Returns the current value of the instance. 
     * 
     * @return array The current value of the ArrayHelper instance
     */

    public function toArray() {
        return $this->value;
    }

 


    /**
     * Returns the number of items in the array. 
     * 
     * @return integer The number of items in the array
     */
    public function getNumberOfArrayItems() {
        return count($this->value);
    }

    /**
     * Gets a random array element
     * 
     * @return object gets a random array element (can be array, string, whatever)
     */
    function getRandomArrayMember() {
        $idx = NumberHelper::getInstance(null)->setLowerLimit(0)->setUpperLimit(count($this->value) - 1)->randomize()->getValue();
        return $this->value[(int) $idx];
    }

    /**
     * Get the item with key $key, if key doesn't exist return $default
     * @param string $key key of item to return
     * @param string default default value to return if item with key $key doesn't exist
     * 
     * @return array The current value of the ArrayHelper instance
     */
    public function getItemWithKey($key, $default = null) {
        if (array_key_exists($key, $this->value)) {
            return $this->value[$key];
        }
        return $default;
    }

       /**
     * Find an item with a specific value for a key in the array of array
     * e.g.: array(
     *              array( "name" => "Melissa", "age" => 40),
     *              array( "name" => "Tim", "age" => 59),
     *              array( "name" => "Bob", "age" => 20),
     *              array( "name" => "Shania", "age" => 20),
     *          )
     * findItemInArrayWithKey("name", "Bob") will return:
     *              array( name = "Bob", age = 20)
     *          
     * findItemInArrayWithKey("age", 20, true) will return:
     *          array(
     *              array( "name" => "Bob", "age" => 20),
     *              array( "name" => "Shania", "age" => 20),
     *          )
     * 
     * @param string $key which arraykey should be inspected
     * @param string $value which value to look for
     * @param boolean $returnall : if false, return the first element found, otherwise return all
     * @return the first found array element
     */
    function findItemInArrayWithKey($key, $value, $returnall = false) {
        $returnvalue = array();
        if (!is_array($this->value) || $key == '')
            return false;

        foreach ($this->value as $v) {
            if (is_array($v) && array_key_exists($key, $v) && $v[$key] == $value) {
                if ($returnall == false) {
                    return $v;
                } else {
                    $returnvalue[] = $v;
                }
            }
        }
        if (count($returnvalue) > 0) {
            return $returnvalue;
        }
        return false;
    }



  // --------------------------------------------------------------------------------------//
    // MODIFIER FUNCTIONS                                                                    //
    // --------------------------------------------------------------------------------------//

    /**
     * Removes duplicate items from the array
     * 
     * @return array The current value of the ArrayHelper instance
     */

    public function removeDuplicates() {
        $serialized = array_map('serialize', $this->value);
        $unique = array_unique($this->value, SORT_REGULAR);
        $this->value = array_intersect_key($this->value, $unique);
        return $this;
    }

    // --------------------------------------------------------------------------------------//
    // CHECKER FUNCTIONS                                                                     //
    // --------------------------------------------------------------------------------------//

    /**
     * This function checks if the array is an associative array (if it has key value pairs:  'name' => 'somename'
     * 
     * @return boolean true or false
     */
    public function isAssociative() {
        if (array() === $this->value) {
            return false;
        }
        return array_keys($this->value) !== range(0, count($this->value) - 1);
    }

    /**
     * 
     * This function checks if all arraykeys defined in $keys exist in the array
     * @param type $keys the keys to check for
     * @return boolean
     */
    
    public function arrayKeysExist($keys) {
    if (is_array($this->getValue())) {
        if (is_array($keys)) {
            $returnvalue = true;
            foreach ($keys as $key) {
                if (!array_key_exists($key, $this->getValue())) {
                    return false;
                }
            }
            return $returnvalue;
        } else {
            return array_key_exists($keys, $this->getValue());
        }
    }
    return false;
}

    
    
    // --------------------------------------------------------------------------------------//
    // SORTING FUNCTIONS                                                                     //
    // --------------------------------------------------------------------------------------//

    /**
     * This function sorts the array by a specific key, it also maintains index association if $keepkeys = true.
     * 
     * @param string $sortkey the key to sort the array by
     * @param string $order SORT_ASC = sort ascending, SORT_DESC = sort descending
     * @param boolean $keepkeys indicates whether to keep the index association
     * @param boolean $casesensitive indicates whether to sort casesensitive or insensitive
     * 
     * @return object the ArrayHelper instance
     */
    public function simpleSort($sortkey = "", $order = SORT_ASC, $keepkeys = true, $casesensitive = true) {
        // Simple function to sort an array by a specific key. Maintains index association if $keepkeys = TRUE.
        $new_array = array();
        $sortable_array = array();

        if (count($this->value) > 0) {
            foreach ($this->value as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $sortkey) {
                            if ($casesensitive == false) {
                                $v2 = mb_strtolower($v2);
                            }
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    if ($casesensitive == false) {
                        $v = mb_strtolower($v);
                    }
                    $sortable_array[$k] = $v;
                }
            }
            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                if ($keepkeys) {
                    $new_array[$k] = $this->value[$k];
                } else {
                    $new_array[] = $this->value[$k];
                }
            }
        }
        $this->value = $new_array;
        return $this;
    }


    /**
     * This function sorts the array by a specific key, it also maintains index association if $keepkeys = true.
     * 
     * @param object $sortstringorarray indicated how you want to sort the array
     *                                  this can be an array array("name" => "asc", "city" => desc)
     *                                  or a string "name asc, date desc" or something similar
     *                                  if no asc or desc is provided, then asc is assumed
     * @param boolean $keepkeys indicates whether to keep the index association
     * @param boolean $casesensitive indicates whether to sort casesensitive or insensitive
     * 
     * @return object the ArrayHelper instance
     */
    public function advancedSort($sortstringorarray, $keepkeys = true, $casesensitive = true) {
        // $sortstringorarray can be either "name asc, date desc" or something similar
        // or an array array("name" => "asc", "date" => "desc")
        // if no asc or desc is provided, then asc is assumed

        if (count($this->value) > 0) {  // if there are no items in the array, don't do anything
            // first normalize the sortstringarray, end up with an array 
            $datasort = null;
            if (is_array($sortstringorarray)) {
                $datasort = $sortstringorarray;
            } else {
                if (strlen($sortstringorarray) > 0) {
                    $datasort = array();
                    $fields = explode(",", $sortstringorarray);
                    foreach ($fields as $fieldinfo) {
                        $fieldinfo = StringHelper::getInstance($fieldinfo)->removeExcessiveWhiteSpace()->toString();
                        $field = explode(" ", trim($fieldinfo));
                        $fieldname = $field[0];
                        if (count($field) > 1) {
                            $sortorder = $field[1];
                        } else {
                            $sortorder = "asc";
                        }
                        $datasort[$fieldname] = $sortorder;
                    }
                }
            }

            // if $datasort is not an array, then the sortstringorarray was not properly formatted, in that case nothing should happen.
            if (is_array($datasort)) {
                // first check if this is an array of arrays, or an array of objects.
                // in other words, can you access array fields with $record->fieldname or with $record[$fieldname]
                // do this outside the foreach loop for performance reasons.
                $arrayOrObject = "A";
                if (count($this->value) > 0) {
                    $arrayrecord1 = $this->value[0];
                    if (is_object($arrayrecord1)) {
                        $arrayOrObject = "O";
                    }
                    if (is_array($arrayrecord1)) {
                        $arrayOrObject = "A";
                    }
                }

                $tmparray = array();
                foreach ($this->value as $recordkey => $record) {
                    $sortkey = "";
                    if ($arrayOrObject == "O") {
                        foreach ($datasort as $key => $order) {
                            if ($order == "desc") {
                                $sortkey .= StringHelper::getInstance($record->$key)->flip()->toString() . "___";
                            } else {
                                $sortkey .= $record->$key . "___";
                            }
                        }
                    } else {
                        foreach ($datasort as $key => $order) {
                            if ($order == "desc") {
                                $sortkey .= StringHelper::getInstance($record[$key])->flip()->toString() . "___";
                            } else {
                                $sortkey .= $record[$key] . "___";
                            }
                        }
                    }
                    // casesensitivity stuff
                    if ($casesensitive == false) {
                        $sortkey = mb_strtolower($sortkey);
                    }
                    $tmparray[$recordkey] = array("sortkey" => $sortkey, "record" => $record);
                }
                $tmparray = ArrayHelper::getInstance($tmparray)->simpleSort("sortkey")->toArray();
                $this->value = array();
                foreach ($tmparray as $arraykey => $arrayelement) {
                    if ($keepkeys) {
                        $this->value[$arraykey] = $arrayelement["record"];
                    } else {
                        $this->value[] = $arrayelement["record"];
                    }
                }
            }
        }
        return $this;
    }


    /**
     * This function does a treesort of the array, it also maintains index association if $keepkeys = true.
     * @param string $idfield the name of the idfield of an array element
     * @param string $parentidfield the name of the parentidfield of an array element
     * @param string $sortfield the field used for sorting the array
     * @param boolean $adddepthfield indicates whether to add a depthfield (= level below top level)
     * @param string $depthfieldname the name of the depthfield, default all added fields are prefixed with double underscore, so default value is __depth
     * @param boolean $addhaschildrenfield indicates whether to add a field indicating if the array element has children
     * @param string $haschildrenfieldname, default value = __haschildren
     * @param boolean $addleftrightfields, indicated whether to add the left and right fields according to the nested set principle
     * @param string $leftfieldname, default value = __left
     * @param string $rightfieldname, default value = __right
     *
     * @return object the ArrayHelper instance
     */
//    public function advancedSort($sortstringorarray, $keepkeys = true, $casesensitive = true) {
        function treeSort($idfield, $parentidfield, $sortfield, $adddepthfield = true, $depthfieldname = "__depth", $addhaschildrenfield = true, $haschildrenfieldname = "__haschildren", $addleftrightfields = true, $leftfieldname = "__left", $rightfieldname = "__right") {
        // addleftrightfields = add a "left" and "right" field according to "nested" set principle.
        $_newarray = array();
        // first we make sure the array key is identical to the ["id"] field of the array.
        // That way, when we want to get an element by id, it's just $array[$id].
        foreach ($this->value as $key => $option) {
            $_newarray[$option[$idfield]] = $option;
        }
        $this->value = $_newarray;
        unset($_newarray);

        $_newarray = array();
        foreach ($this->value as $option) {
            if (!array_key_exists("__sortfield", $option)) {
                // this option has not been processed yet
                $_sortfield = $option[$sortfield] . "_" . $option[$idfield];
                $_parentid = $option[$parentidfield];
                $_depth = 0;
                $_counter = 0;
                while ($_parentid !== $option[$idfield] && $_parentid > 0 && is_numeric($_parentid) && $_parentid !== null && array_key_exists($_parentid, $this->value)) {
                    $_tmpoption = $this->value[$_parentid];
                    $_sortfield = $_tmpoption[$sortfield] . "_" . $_tmpoption[$idfield] . " | " . $_sortfield;
                    $_parentid = $_tmpoption[$parentidfield];
                    $_depth += 1;
                    $_counter += 1;
                }
                if ($adddepthfield) {
                    $option[$depthfieldname] = $_depth;
                }
                if ($addleftrightfields) {
                    $option[$leftfieldname] = 0; // init, set to 0
                    $option[$rightfieldname] = 0;
                }
                $option["__sortfield"] = $_sortfield;
            }
            $_newarray[$option[$idfield]] = $option;
        }
        $this->value = $_newarray;
        unset($_newarray);

        // sort the array on __sortfield to get the correct sortorder and renumber the keys
        $this->simpleSort("__sortfield", SORT_ASC, false);
        // second get an array with all the array keys
        $_arrkeys = array();
        $_db_id_to_counter_lookup_array = array();
        foreach ($this->value as $_key => $_option) {
            $_arrkeys[$_key] = $_option[$idfield];
            $_db_id_to_counter_lookup_array[$_option[$idfield]] = $_key;
        }

        if ($addhaschildrenfield) {
            $oldkey = null;
            $oldsortfield = null;
            foreach ($this->value as $key => $option) {
                $this->value[$key][$haschildrenfieldname] = false;
                $thissortfield = $option["__sortfield"];
                if ($oldsortfield !== null) {
                    if (strpos($thissortfield, strval($oldsortfield)) !== false) {
                        $this->value[$oldkey][$haschildrenfieldname] = true;
                    }
                }
                $oldkey = $key;
                $oldsortfield = $thissortfield;
            }
        }
        if ($addleftrightfields) {
            // --------------------------------------------------------------
            // now we have to loop over all elements in $this-value (in the order defined in $_counter_to_idx_lookup_array)
            // and fill in the left and right fields
            $stoploop = false;
            $i = 1;
            $recordidx = 0;
            $nrofrecs = count($this->value);
            $endloop = $nrofrecs * 2;
            for ($i = 1; $i <= $endloop; $i++) {
                // ------------------------------------------------------------------------------------------
                // $recordidx indicates the record in the $_arrkeys array-index of the record we currently have to process
                $idx = $recordidx;
                $_value_index = $_arrkeys[$idx];
                // ------------------------------------------------------------------------------------------
                // ------------------------------------------------------------------------------------------
                // first fill in the appropriate field, we HAVE to fill in something, either the left field or the right field
                // if left is still empty, then fill in left, else fill in right
                if ($this->value[$idx][$leftfieldname] == 0) {
                    $this->value[$idx][$leftfieldname] = $i;
                } else {
                    if ($this->value[$idx][$rightfieldname] == 0) {
                        $this->value[$idx][$rightfieldname] = $i;
                    }
                }
                // ------------------------------------------------------------------------------------------
                // ------------------------------------------------------------------------------------------
                // nextidx should be the $_tmparray index of the next 'not completely filled' record. So, the first record where either left or right is empty.
                // since left will always be filled if right is filled, we can check for only right = empty (right will also be empty when left is empty too)
                $nextidx = $recordidx + 1; // $nextidx = the index in the $_counter_to_idx_lookup_array
                if ($nextidx < count($_arrkeys)) {
                    $checkidx = $nextidx; // $checkix = $nextidx translated to the correct index for lookup in the $this->value array
                    while ($nextidx < $nrofrecs && $this->value[$checkidx][$rightfieldname] !== 0) {
                        $nextidx++;
                        if ($nextidx < count($_arrkeys)) {
                            $checkidx = $nextidx;
                        }
                    }
                }
                // ------------------------------------------------------------------------------------------
                // ------------------------------------------------------------------------------------------
                // we need the sortfields of this record and our next record for testing purposes
                $thissortfield = $this->value[$idx]["__sortfield"];
                if ($nextidx < $nrofrecs) {
                    $nextsortfield = $this->value[$nextidx]["__sortfield"];
                } else {
                    $nextsortfield = "";
                }
                // ------------------------------------------------------------------------------------------
                // ------------------------------------------------------------------------------------------
                // Now we need to determine what our next record will be
                // The rules:
                // If there is a child available, always use the child
                // If no child is available
                //       and the current record's rightfield is empty, use current record
                //       and the current record's rightfield is filled
                //           if there are siblings available, use the sibling
                //           if no sibling is availble
                //                 and a parent is found that is not completely filled, use the parent
                //                 else : we must have processed complete tree
                if (strpos($nextsortfield, $thissortfield) !== false) {
                    // if next record == child, take child
                    $recordidx = $nextidx;
                } else {
                    // next record !== child, check if current record rightfield is filled
                    if ($this->value[$idx][$rightfieldname] == 0) {
                        // don't change $recordix, we should first completely fill this record
                    } else {
                        // and next record == sibling
                        if (($nextidx < $nrofrecs) && ($this->value[$idx][$parentidfield] == $this->value[$nextidx][$parentidfield])) {
                            $recordidx = $nextidx;
                        } else {
                            // then we have to first check for a parent record which has not yet been
                            // completely filled.
                            // If we find such a record, than this is our next record to process
                            $_chkparentid = $this->value[$idx][$parentidfield];
                            $_continue = true;
                            while ($_continue == true) {
                                if (($_chkparentid !== 0) && ($_chkparentid !== "") && ($_chkparentid !== null)) {
                                    if ($this->value[$_db_id_to_counter_lookup_array[$_chkparentid]][$rightfieldname] == 0) {
                                        $recordidx = $_db_id_to_counter_lookup_array[$_chkparentid];
                                        $_continue = false;
                                    } else {
                                        $_chkparentid = $this->value[$_db_id_to_counter_lookup_array[$_chkparentid]][$parentidfield];
                                    }
                                } else {
                                    $_continue = false;
                                }
                            }
                        }
                    }
                }

                // ------------------------------------------------------------------------------------------
            }
        }
        return $this;
    }




}




/*
  

    private function convertToUTF8() {
        array_walk_recursive($this->value, function(&$item, $key) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
            }
        });
        return $this;
    }
    public function toJSON() {
        $returnvalue = convertToJSON($this->value);
        if (strlen($returnvalue) < 3 && count($this->value) > 0) {
            $this->convertToUTF8();
            $returnvalue = convertToJSON($this->value);
        }
        return $returnvalue;
    }


    function explode($delimiter, $surroundwithquotes = true, $key = "@all") {
        $returnvalue = "";
        $insert = "";
        $quote = "";
        if ($surroundwithquotes == true) {
            $quote = "'";
        }
        $idx = count($this->value);
        for ($i = 0; $i < $idx; $i++) {
            if ($key !== "@all") {
                $returnvalue = $returnvalue . $insert . $quote . $this->value[$i][$key] . $quote;
            } else {
                $returnvalue = $returnvalue . $insert . $quote . $this->value[$i] . $quote;
            }
            $insert = $delimiter;
        }
        return $returnvalue;
    }

    function implode($glue, $pieces) {
        $return = "";

        if (!is_array($glue)) {
            $glue = array($glue);
        }

        $thisLevelGlue = array_shift($glue);

        if (!count($glue))
            $glue = array($thisLevelGlue);

        if (!is_array($pieces)) {
            return (string) $pieces;
        }

        foreach ($pieces as $sub) {
            $return .= $this->implode($glue, $sub) . $thisLevelGlue;
        }

        if (count($pieces))
            $return = substr($return, 0, strlen($return) - strlen($thisLevelGlue));

        return $return;
    }

 
    function debug_print_tree_array() {
        foreach ($this->value as $option) {
            for ($i = 0; $i <= $option['depth']; $i++) {
                echo "___";
            }
            echo $option['name'] . ' (id:' . $option['id'] . "|parent:" . $option['parentid'] . "|sortfield:" . $option['__sortfield'] . "|key: " . $option['_key'] . "|depth:" . $option['depth'] . "|left:" . $option['left'] . '|right:' . $option['right'] . ")";
            echo "<br>";
        }
    }

    function swapKeys($oldkey, $newkey) {
        // probably doesn't work...
        if (isset($this->value[$newkey])) {
//echo "isset<br>";
            $_newelement = $this->value[$newkey];
            $_oldelement = $this->value[$oldkey];
            unset($this->value[$oldkey]);
            unset($this->value[$newkey]);
            $this->value[$newkey] = $_oldelement;
            $this->value[$oldkey] = $_newelement;
        } else {
//echo "not isset<br>";
            $this->value[$newkey] = $this->value[$oldkey];
            unset($this->value[$oldkey]);
        }
    }



}
*/