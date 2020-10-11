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
        view("master.view", ["title" => TITLE, 'include' => 'index.view', 'user' => Auth::user()]);
    }

    /**
     * Show the data from a protected route
     */
    function showAPI()
    {
        response(['success' => true, 'page' => 'dashboard']);
    }
}
