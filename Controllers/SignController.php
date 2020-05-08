<?php

namespace Controllers;

use Core\Controllers;
use Core\Auth;

class SignController extends Controllers
{
    /**
     * Show view for signup form
     */
    public function show()
    {
        if (Auth::user()) {
            redirect()->route('dashboard');
        }
        view("master.view", ["title" => TITLE . " - Cadastro", 'include' => 'sign.view']);
    }
    /**
     * Initiates the signup
     */
    public function sign()
    {
        $auth = Auth::sign();
        if ($auth !== true) {
            redirect()->route('show.signup', ['errors' => $auth]);
        } else {
            redirect()->route('dashboard');
        }
    }
}
