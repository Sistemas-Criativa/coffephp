<?php

namespace Core;

class Query{
    /**
     * Set a value
     */
    public function __set($atrib, $value){
        $this->$atrib = $value;
    }

    /**
     * Get a value
     */
    public function __get($atrib){
        return $this->$atrib;
    }
}
?>