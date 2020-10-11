<?php

namespace Core;

use Core\Auth;
use Core\Validator;
use Config\Config;

class Filter
{
    //Array of filters and urls in case fails, the API prop, return the response as jsom. The redirect prop is just valid if api is false
    public $filters = [
        'authByAPI' => ['api' => true],
        'auth' => ['api' => false, 'redirect' => '/login']
    ];

    /**
     * Verify if user is logged
     */
    public function auth()
    {
        return (Auth::authFilter() == false ? false : true);
    }

    /**
     * Verify if user is logged using token
     */
    public function authByAPI()
    {
        $authByAPI = Auth::token();
        return ($authByAPI === true ? true : $this->returnError($authByAPI, Auth::$error_type));
    }

    private function returnError($errors, $error_type = 400)
    {
        return ['success' => false, 'errorcode' => 1, 'errors' => $errors, 'http_status' => $error_type];
    }
}
