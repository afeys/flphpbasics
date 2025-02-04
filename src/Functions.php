<?php
// this php file contains various simple helperfunctions

namespace FL;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class Functions {

    static function utf8_decode($string) {
        if ($string !== null && is_string($string)) {
            return mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
        }
        return $string; // Return original value for non-strings
    }

    static function utf8_encode($string) {
        if ($string !== null && is_string($string)) {
            return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
        }
        return $string; // Return original value for non-strings
    }

    static function toUTF8($d) {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = Functions::toUTF8($v);
            }
        } else if (is_object($d)) {
            foreach ($d as $k => $v) {
                $d->$k = Functions::toUTF8($v);
            }
        } else if (is_string($d)) {
            return Functions::utf8_encode($d);
        }
        return $d; // Return unchanged for non-strings
    }
    static function if($condition, $iftrue = true, $iffalse = true) {
        if ($condition) {
            return $iftrue;
        }
        return $iffalse;
    }

    static function ifEmptyThen($valuetocheck, $valuetoreturn) {
        if ($valuetocheck == null || $valuetocheck == "") {
            return $valuetoreturn;
        }
        return $valuetocheck;
    }

    static function isEmpty($value) {
        if ($value === null) {
            return true;
        }
        if ($value === "") {
            return true;
        }
        if ($value === 0) {
            return true;
        }
        if ($value === "0") {
            return true;
        }
        return false;
    }
    /**
     * @throws ReflectionException
     */
    static function hasStaticProperty($className, $propertyName): bool
    {
        $reflectionClass = new ReflectionClass($className);
        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_STATIC) as $property) {
            if ($property->getName() === $propertyName) {
                return true;
            }
        }
        return false;
    }
    static function tabs($nroftabs) {
        return str_repeat("\t", $nroftabs);
    }

    static function spaces($nrofspaces) {
        return str_repeat("\t", $nrofspaces);
    }

    static function isValidDomain($domain) {
        if (filter_var($domain, FILTER_VALIDATE_DOMAIN)) {
            return true;
        }
        return false;
    }

// this function will check if mailaddress is a valid mailaddress
    static function isValidMailAddress($mailaddress) {
        if (filter_var($mailaddress, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }
}