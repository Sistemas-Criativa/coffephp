<?php
namespace Core;
class View {
    const ext = ".php";
    public static $Route;
    /**
     * Run a view
     */
    protected final function view(string $View,array $args = array()){
        //get the args
        foreach($args as $arg => $value){
            $$arg = $value;
        }
        $separator = array('\\', '/');
        $View = str_replace($separator, DIRECTORY_SEPARATOR, $View);

        //verify if file exists
        if(file_exists($View.self::ext))
        {
            include_once($View.self::ext);
        } else {
            throw new \Exception("View '$View' not found");
        }
    }
}
?>