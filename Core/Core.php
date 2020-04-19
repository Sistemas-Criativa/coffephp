<?php

namespace Core;

use Core\Route;
use Core\Request;
use Core\Utilities;

class Core extends Route
{
    function __construct()
    {
        session_start();
        if(Request::session('internal_token')==false)
        {
            Request::saveDataSession('internal_token', Utilities::token());
        }
        include_once("../Routes" . DIRECTORY_SEPARATOR . "web.php");
        //run the Routes
        Route::Routes();
    }
}
