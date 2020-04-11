<?php
namespace Core;
use Core\Route;
class Core extends Route{
    function __construct() {
        session_start();
        include_once("Routes".DIRECTORY_SEPARATOR."web.php");
        //run the Routes
        Route::Routes();
    }
}
?>