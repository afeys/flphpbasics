<?php
// this php file contains various simple helperfunctions

namespace FL;
class Functions {
    static function if($condition, $iftrue = true, $iffalse = true) {
        if ($condition) {
            return $iftrue;
        }
        return $iffalse;
    }
}