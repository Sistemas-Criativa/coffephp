<?php

use Core\Request;

function redirect($location)
{
    header('location: ' . $location);
}
function back()
{
    header('location: ' .  Request::server('REQUEST_URI'));
}
