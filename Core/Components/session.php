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
function flashed(){
    return Request::session('flashed') ?? null;
}
