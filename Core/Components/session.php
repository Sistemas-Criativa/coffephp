<?php

use Core\Request;

function session($item = '')
{
    return Request::session($item);
}
