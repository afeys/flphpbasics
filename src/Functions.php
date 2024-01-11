<?php

namespace FL;

function if($condition, $iftrue, $iffalse) {
    if ($condition) {
        return $iftrue;
    }
    return $iffalse;
}