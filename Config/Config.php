<?php

namespace Config;

class Config
{
    private $option = [
        "host" => "localhost",
        "user" => "root",
        "base" => "",
        "session" => "coffePHP",
        "password" => "",
        "Connection" => null
    ];

    private static $instance = null;
    private $Connection;

    /**
     *  define a Conection com o servidor 
     * 
     * */
    private function __construct()
    {
        $this->Connection = new \mysqli($this->option['host'], $this->option['user'], $this->option['password'], $this->option['base']);
        $this->Connection->set_charset("UTF8");
        if ($this->Connection->connect_error) {
            exit;
        }
    }

    /** get instance from class */
    public static function Instance()
    {
        if (!self::$instance) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    /** get a Connection*/
    public function Connection()
    {
        return $this->Connection;
    }

    /**
     * Get the session name
     */
    public final static function session()
    {
        return (new static)->option['session'];
    }

    /**
     * Get the config
     */
    public final static function config($name = ''){
        if(empty($name)){
            return (new static)->option;
        } else {
            if(isset((new static)->option[$name])) {
                return (new static)->option[$name];
            }
            return null;
        }
        
    }
}
