<?php
// this php file contains various simple helperfunctions

namespace FL;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class Functions {
    static function if($condition, $iftrue = true, $iffalse = true) {
        if ($condition) {
            return $iftrue;
        }
        return $iffalse;
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

}