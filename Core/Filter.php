<?php

namespace Core;

use Core\Auth;

class Filter
{
    //Array of filters and urls in case fails
    public $filters = ['auth' => '/login'];

    /**
     * Verify if user is logged
     */
    public function auth()
    {
        return (Auth::user() == false ? false : true);
    }
}
