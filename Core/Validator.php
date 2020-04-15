<?php

namespace Core;

use Exception;

class Validator
{
    private static $errors = array();
    /**
     * Generate a hash sha512
     */
    public final static function validate(array $fields, array $rules)
    {
        foreach ($rules as $item => $value) {
            if (is_array($value)) {
                foreach($value as $rule){
                    $method = explode(":", $rule);
                    $args = substr($method[0], strpos($method[0],"#")+1);
                    $methodName = (strpos($method[0],"#")?substr($method[0], 0,strpos($method[0],"#")):$method[0]);
                    if (method_exists(self::class, $methodName)) {
                        $function = $methodName;
                        $message = (isset($method[1]) ? $method[1] : "");
                        self::$function($item, $fields, $message, $args);
                    } else {
                        throw new \Exception("The rule '$method[0]' not exists");
                    }   
                }    
            } else {
                throw new \Exception("The rule for '$item' must be an array");
            }
        }
        return new static;
    }

    /**
     * Verify if a field is required
     */
    private static function required($item, $fields, $message)
    {
        if (!array_key_exists($item, $fields)) {
            self::$errors[] = (empty($message) ? "The field '$item' is required" : $message);
        }
    }
    /**
     * Verify the max quantity for character
     */
    private static function max($item, $fields, $message, $args){
        if (array_key_exists($item, $fields)) {
            if(strlen($fields[$item]) > $args){
                self::$errors[] = (empty($message) ? "The max quantity for '$item' is " . $args : $message);
            }
        }
    }

    /**
     * Verify the min quantity for character
     */
    private static function min($item, $fields, $message, $args){
        if (array_key_exists($item, $fields)) {
            if(strlen($fields[$item]) < $args){
                self::$errors[] = (empty($message) ? "The min quantity for '$item' is " . $args : $message);
            }
        }
    }

    /**
     * Verify if is a valid e-mail
     */
    private static function email($item, $fields, $message){
        if (array_key_exists($item, $fields)) {
            if(!filter_var($fields[$item], FILTER_VALIDATE_EMAIL)){
                self::$errors[] = (empty($message) ? "The e-mail is invalid" : $message);
            }
        }
    }
    /**
     * Verify if is boolean
     */
    private static function bool($item, $fields, $message){
        if (array_key_exists($item, $fields)) {
            if(!filter_var($fields[$item], FILTER_VALIDATE_BOOLEAN)){
                self::$errors[] = (empty($message) ? "The field '$item' is not a boolean" : $message);
            }
        }
    }
    /**
     * Verify if is domain
     */
    private static function domain($item, $fields, $message){
        if (array_key_exists($item, $fields)) {
            if(!filter_var($fields[$item], FILTER_VALIDATE_DOMAIN)){
                self::$errors[] = (empty($message) ? "The field '$item' is not a valid domain" : $message);
            }
        }
    }
    /**
     * Verify if is float
     */
    private static function float($item, $fields, $message){
        if (array_key_exists($item, $fields)) {
            if(!filter_var($fields[$item], FILTER_VALIDATE_FLOAT)){
                self::$errors[] = (empty($message) ? "The field '$item' is not a float" : $message);
            }
        }
    }
    /**
     * Verify if is INT
     */
    private static function int($item, $fields, $message){
        if (array_key_exists($item, $fields)) {
            if(!filter_var($fields[$item], FILTER_VALIDATE_INT)){
                self::$errors[] = (empty($message) ? "The field '$item' is not a int" : $message);
            }
        }
    }
    /**
     * Verify if is IP
     */
    private static function ip($item, $fields, $message){
        if (array_key_exists($item, $fields)) {
            if(!filter_var($fields[$item], FILTER_VALIDATE_IP)){
                self::$errors[] = (empty($message) ? "The field '$item' is not a valid IP" : $message);
            }
        }
    }
    /**
     * Verify if is MAC
     */
    private static function mac($item, $fields, $message){
        if (array_key_exists($item, $fields)) {
            if(!filter_var($fields[$item], FILTER_VALIDATE_MAC)){
                self::$errors[] = (empty($message) ? "The field '$item' is not a valid MAC" : $message);
            }
        }
    }
    /**
     * Verify if is URL
     */
    private static function url($item, $fields, $message){
        if (array_key_exists($item, $fields)) {
            if(!filter_var($fields[$item], FILTER_VALIDATE_URL)){
                self::$errors[] = (empty($message) ? "The field '$item' is not a valid URL" : $message);
            }
        }
    }
    public function errors()
    {
        if (sizeof(self::$errors) == 0) {
            return false;
        } else {
            return self::$errors;
        }
    }
}
