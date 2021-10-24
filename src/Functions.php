<?php

namespace FL;

function arrayKeysExist($array, $keys) {
    if (is_array($array)) {
        if (is_array($keys)) {
            $returnvalue = true;
            foreach ($keys as $key) {
                if (!array_key_exists($key, $array)) {
                    return false;
                }
            }
            return $returnvalue;
        } else {
            return array_key_exists($keys, $array);
        }
    }
    return false;
}


function toUTF8($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = toUTF8($v);
        }
    } else {
        if (is_object($d)) {
            foreach ($d as $k => $v) {
                $d->$k = toUTF8($v);
            }
        } else {
            return utf8_encode($d);
        }
    }
    return $d;
}

// this function will check if $url is a valid url
function isValidUrl($url) {
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return true;
    }
    return false;
}

// this function will check if $domain is a valid domain
function isValidDomain($domain) {
    if (filter_var($domain, FILTER_VALIDATE_DOMAIN)) {
        return true;
    }
    return false;
}

// this function will check if mailaddress is a valid mailaddress
function isValidMailAddress($mailaddress) {
    if (filter_var($mailaddress, FILTER_VALIDATE_EMAIL)) {
        return true;
    }
    return false;
}

function convertToJSON($data) {
    return json_encode(toUTF8($data));
}

function isEmpty($value) {
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

function emptyToSpace($value) {
    if ($value === null) {
        $value = "";
    }
    if ($value === 0) {
        $value = "";
    }
    if ($value === "0") {
        $value = "";
    }
    return $value;
}

function requireFile($file) {
    if (file_exists($file)) {
        require $file;
    }
}
