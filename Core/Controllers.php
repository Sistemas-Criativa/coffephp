<?php
namespace Core;
use Core\View;
use Core\Route;
class Controllers extends View{
    function __construct(Route $self)
    {
        self::$Route = $self;
    }
}
?>