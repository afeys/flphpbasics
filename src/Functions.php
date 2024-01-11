<?php

namespace flphpbasics;

function if($condition, $iftrue, $iffalse) {
    if ($condition) {
        return $iftrue;
    }
    return $iffalse;
}