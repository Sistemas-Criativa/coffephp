<?php

namespace Controllers;

use Core\Controllers;
use Core\Auth;

class LoginController extends Controllers
{
    /**
     * Show view for login form
     */
    public function show()
    {
        if (Auth::user()) {
            redirect()->route('dashboard');
        }
        view("master.view", ["title" => "Login - CoffePHP", 'include' => 'login.view']);
    }

    /**
     * Do the login
     */
    public function login()
    {
        $auth = Auth::auth();
        if (Auth::auth() !== true) {
            redirect()->route('show.login', ['errors' => $auth]);
        } else {
            redirect()->route('dashboard');
        }
    }

    public function logout()
    {
        Auth::logout();
        redirect()->route('home');
    }
}
