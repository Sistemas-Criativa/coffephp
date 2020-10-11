<?php

namespace Core;

use Config\Config;

class Request
{
    private $request = null;
    function __construct()
    {
        $this->request = $_REQUEST;
    }

    /**
     * Get a server variable
     */
    public final static function server(string $item = "")
    {
        if (!empty($item)) {
            if (isset($_SERVER[$item])) {
                return $_SERVER[$item];
            } else {
                return null;
            }
        } else {
            return $_SERVER;
        }
    }

    /**
     * get a request value
     */
    public final function request(string $item = "")
    {
        if (!empty($item)) {
            if (isset($this->request[$item])) {
                return $this->request[$item];
            } else {
                return null;
            }
        } else {
            return $this->request;
        }
    }

    /**
     * Insert query params to get
     */
    public final static function query(string $request): void
    {
        $params = explode("&", substr($request, strpos($request, "?") + 1));
        foreach ($params as $item) {
            $values = explode("=", $item);
            $_GET[$values[0]] = $values[1];
        }
    }

    public static final function sanitize(string $value)
    {
        return filter_var($value, FILTER_SANITIZE_STRING);
    }
    /**
     * retorna o método da requisição
     */
    public static final function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * retorna um ou todos os valuees do get
     */
    public final static function get(array $itens = array())
    {
        $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
        if (count($itens) > 0) {
            $temp = [];
            foreach ($itens as $item) {
                if (isset($_GET[$item])) {
                    $temp[$item] = $_GET[$item];
                }
            }
            return $temp;
        } else {
            return $_GET;
        }
    }

    /**
     * get all or a specific header
     */
    public final static function headers(array $itens = array()){
        $allHeaders = getallheaders();
        if(count($itens) > 0) {
            $temp = [];
            foreach($itens as $item){
                if(array_key_exists($item, $allHeaders)){
                    $temp[] = $allHeaders[$item];
                }
            }
            return $temp;
        } else {
            return $allHeaders;
        }
    }
    /**
     * Return all or a specific item of post
     */
    public final static function post(array $itens = array())
    {
        $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $json = file_get_contents('php://input');
        $json = json_decode($json, true);

        if (count($itens) > 0) {
            $temp = [];
            foreach ($itens as $item) {
                if (isset($_POST[$item])) {
                    $temp[$item] = $_POST[$item];
                } else if (isset($json[$item])) {
                    $temp[$item] = $json[$item];
                }
            }
            return $temp;
        } else {
            $temp = [];
            if ($_POST != null) {
                foreach ($_POST as $item => $value) {
                    $temp[$item] = $value;
                }
            }
            if ($json != null) {
                foreach ($json as $item => $value) {
                    $temp[$item] = $value;
                }
            }
            return $temp;
        }
    }

    /**
     * Save data in a session
     */
    public final static function saveDataSession(string $identifier, $value, bool $append = false, string $sessionName = "")
    {
        if ($append) {
            $_SESSION[(empty($sessionName) ? Config::session() : $sessionName)][$identifier][] = $value;
        } else {
            $_SESSION[(empty($sessionName) ? Config::session() : $sessionName)][$identifier] = $value;
        }
    }

    /**
     * Unset a session
     */
    public final static function clearDataSession(string $identifier, string $sessionName = "")
    {
        unset($_SESSION[(empty($sessionName) ? Config::session() : $sessionName)][$identifier]);
    }

    /**
     * Destroy the sessions
     */
    public final static function destroySession()
    {
        session_destroy();
    }

    /**
     * Get session data
     */
    public final static function session(string $identifier, string $sessionName = "")
    {
        if (isset($_SESSION[(empty($sessionName) ? Config::session() : $sessionName)][$identifier])) {
            return $_SESSION[(empty($sessionName) ? Config::session() : $sessionName)][$identifier];
        } else {
            return false;
        }
    }

    /**
     * Redirect
     */
    public final static function redirect($url = "")
    {
        if (empty($url)) {
            return (new static);
        } else {
            self::url($url);
        }
    }

    /**
     * Redirect back to origin page
     */
    public final static function back(array $args = array())
    {
        self::saveDataSession('flashed', $args);
        header('location: ' .  Request::server('REQUEST_URI'));
    }

    /**
     * redirect to a route
     */
    public final static function route($route, array $args = array())
    {
        self::saveDataSession('flashed', $args);
        header('location: ' .  Route::route($route, true, $args));
    }

    /**
     * redirect to an url
     */
    public final static function url($url)
    {
        header('location: ' .  $url);
    }

    /**
     * allow access from helper
     */
    public final static function getRequests(){
        return new static;
    }
}
