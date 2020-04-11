<?php
namespace Core;
use Config\Config;
class Request {
    private $request = null;
    private $server = null;
    function __construct()
    {
        $this->request = $_REQUEST;
        $this->server = $_SERVER;
    }

    /**
     * Get a server variable
     */
    public final function server(string $item = ""){
        if(!empty($item))
        {
            if(isset($this->request[$item])) {
                return $this->server[$item];
            } else {
                return null;
            }
        } else {
            return $this->server;
        }
    }

    /**
     * get a request value
     */
    public final function request(string $item = ""){
        if(!empty($item))
        {
            if(isset($this->request[$item])) {
                return $this->request[$item];
            } else {
                return null;
            }
        } else {
            return $this->request;
        }
        
    }

    /**
     * Insere os valuees no get
     */
    public final static function gets(string $request){
        $params = explode("&",substr($request,strpos($request,"?")+1));
        foreach($params as $item){
            $valuees = explode("=",$item);
            $_GET[$valuees[0]] = $valuees[1];
        }
    }

    /**
     * retorna o método da requisição
     */
    public final function method(){
        return $this->server['REQUEST_METHOD'];
    }

    /**
     * retorna um ou todos os valuees do get
     */
    public final function get(array $itens = array()){
        if(count($itens)>0)
        {
            $temp = [];
            foreach($itens as $item){
                if(isset($_GET[$item])) {
                    $temp[$item] = $_GET[$item];
                }
            }
            return $temp;
        } else {
            return $_GET;
        }
    }

    /**
     * Return all or a specific item of post
     */
    public final function post(array $itens = array()){
        if(count($itens)>0)
        {
            $temp = [];
            foreach($itens as $item){
                if(isset($_POST[$item])) {
                    $temp[$item] = $_POST[$item];
                }
            }
            return $temp;
        } else {
            return $_GET;
        }
    }

    /**
     * Save data in a session
     */
    public final static function saveSession($value, string $sessionName = ""){
        $_SESSION[(empty($sessionName) ? Config::session() : $sessionName)] = $value;
    }

        /**
     * Unset a session
     */
    public final static function clearSession(string $sessionName = ""){
        unset($_SESSION[(empty($sessionName) ? Config::session() : $sessionName)]);
    }

    /**
     * Destroy the sessions
     */
    public final static function destroySession(){
        session_destroy();
    }

    /**
     * Get session data
     */
    public final static function session(string $sessionName = ""){
        if(isset($_SESSION[(empty($sessionName) ? Config::session() : $sessionName)])){
            return $_SESSION[(empty($sessionName) ? Config::session() : $sessionName)];
        } else {
            return false;
        }
    }
}
?>