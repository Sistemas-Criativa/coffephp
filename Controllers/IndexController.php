<?php

namespace Controllers;

use Core\Controllers;
use Core\Auth;

class IndexController extends Controllers
{

    /**
     * Show the view for index
     */
    function show()
    {
        view("master.view", ["title" => "Welcome to CoffePHP", 'include' => 'index.view', 'user' => Auth::user()]);
    }
}
