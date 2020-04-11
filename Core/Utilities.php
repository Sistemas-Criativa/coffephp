<?php
namespace Core;

class Utilities {
    /**
     * Generate a hash sha512
     */
    public final static function hash(string $value){
        return hash("SHA512", $value);
    }

    /**
     * Generate a token
     */
    public final static function token(){
        $token = self::hash(rand(111111,999999) . " - " . date("Y-m-d-H-i-s"));
        return $token;
    }
}
?>