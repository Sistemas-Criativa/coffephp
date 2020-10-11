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
        view("master.view", ["title" => TITLE . " - Login", 'include' => 'login.view']);
    }

    /**
     * Do the login
     */
    public function login()
    {
        $auth = Auth::auth();
        if ($auth !== true) {
            redirect()->route('show.login', ['errors' => $auth]);
        } else {
            redirect()->route('dashboard');
        }
    }

    /**
     * Do the login
     */
    public function loginAPI()
    {
        $auth = Auth::auth();
        if ($auth !== true) {
            response(['success' => false, 'errors' => ['errors' => $auth]], 401);
        } else {
            response(['success' => true, 'user' => Auth::user()], 200);
        }
    }

    public function logout()
    {
        Auth::logout();
        redirect()->route('home');
    }
}
