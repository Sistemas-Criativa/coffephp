<?php

namespace Core;

use Core\Route;

class Controllers
{
    function __construct(Route $self)
    {
        global $Route;
        $Route = $self;
    }
}
