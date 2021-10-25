<?php

namespace FL;

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
