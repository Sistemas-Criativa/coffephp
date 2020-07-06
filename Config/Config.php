<?php

namespace Config;

class Config
{
    private static $instance = null;
    private $Connection;

    /**
     *  define a Conection com o servidor 
     * 
     * */
    private function __construct()
    {
        global $options;
        $this->Connection = new \mysqli($options['host'], $options['user'], $options['password'], $options['base']);
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
        global $options;
        return $options['session'];
    }

    /**
     * Get the config
     */
    public final static function config($name = '')
    {
        global $options;
        if (empty($name)) {
            return $options;
        } else {
            if (isset($options[$name])) {
                return $options[$name];
            }
            return null;
        }
    }
}
