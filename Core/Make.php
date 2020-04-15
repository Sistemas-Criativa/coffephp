<?php

namespace Core;

class Make
{
    public static function make($controller, $methodName, $args = array(), $constructor = null)
    {
        //Init the classe
        $classe = new $controller($constructor);
        // var_dump($controller);
        //verifica se existe o método
        if (method_exists($classe, $methodName)) {
            //get class i nformations
            $class = new \ReflectionClass($controller);

            //get the method
            $method = $class->getMethod($methodName);
            $totalparams = 0;

            //get parameters
            $params = $method->getParameters();
            $totalparams = sizeof($params);

            //verify the params
            for ($i = 0; $i < sizeof($params); $i++) {

                //if the parameter is a class
                if (isset($method->getParameters()[$i]->getClass()->name)) {
                    //verify if is optional
                    if ($method->getParameters()[$i]->isOptional()) {
                        $args[] = null;
                    } else {
                        //Instantiate a class and save to list
                        $class = $method->getParameters()[$i]->getClass()->name;
                        $args[] = new $class;
                    }
                } else {
                    //get the default value to optional variables
                    if ($method->getParameters()[$i]->isDefaultValueAvailable())
                        $args[] = $method->getParameters()[$i]->getDefaultValue();
                }
            }

            //verify if the args of a function is equals
            if (count($args) == $totalparams) {
                $classe->$methodName(...$args);
            } else {
                throw new \Exception("The method '$methodName' of class '$controller' needs $totalparams args, but just sent " . count($args) . ".");
            }
        } else {
            throw new \Exception("The class '$controller' don't have a method named '$methodName'");
        }
    }
}
