<?php
// this php file contains various simple helperfunctions

namespace FL;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class Functions {
    static function toUTF8($d) {
        if (is_array($d))
            foreach ($d as $k => $v)
                $d[$k] = toUTF8($v);

        else if (is_object($d))
            foreach ($d as $k => $v)
                $d->$k = toUTF8($v);
        else
            return utf8_encode($d);

        return $d;
    }
    static function if($condition, $iftrue = true, $iffalse = true) {
        if ($condition) {
            return $iftrue;
        }
        return $iffalse;
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

}