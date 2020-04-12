<?php
    namespace Config;
    class Config{
        private static $instance = null;
        /*Variáveis de Conection*/
        private $host = "localhost";
        private $user = "root";
        private $base = "";
        private static $session = "";
        private $password = "";
		private $Connection;

        /**
         *  define a Conection com o servidor 
         * 
         * */
        private function __construct(){
            $this->Connection = new \mysqli($this->host, $this->user, $this->password, $this->base);
			$this->Connection->set_charset("UTF8");
			if($this->Connection->connect_error){
				exit;
			}
        }

        /** get instance from class */
        public static function Instance(){
            if(!self::$instance){
                self::$instance = new Config();
            }
            return self::$instance;
        }

        /** get a Connection*/
        public function Connection(){
            return $this->Connection;
        }

        /**
         * Get the session name
         */
        public final static function session(){
            return self::$session;
        }

    }
?>