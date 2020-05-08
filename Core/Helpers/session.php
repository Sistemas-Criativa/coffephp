<?php

use Core\Request;

/**
 * Get a session item
 */
function session($item = '')
{
    return Request::session($item);
}

/**
 * get a flashed session item
 */
function flashed(string $identifier = ''){
    if(empty($identifier)) {
        return Request::session('flashed');
    } else {
        $flashed = Request::session('flashed');
        if(isset($flashed[$identifier])){
            return $flashed[$identifier];
        } else {
            return false;
        }
    }
    
}
