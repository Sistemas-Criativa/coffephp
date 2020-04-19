<?php

use Core\Request;
/**
 * Write a auth token
 */
function token(){
    return Request::session('internal_token');
}
?>