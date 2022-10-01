<?php

namespace App\Classes;

### Not currently used. Need to do more research to make this work correctly.
spl_autoload_register(function($class){
        
    $exp = explode("\\", $class);

    if (!is_array($exp)){
        return null;
    }
    
    $className = end($exp); //Get the final element in the array (e.g. App/Class/xxxxx => xxxxxx)

    //$className = str_replace("\\", DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . DIRECTORY_SEPARATOR . $className . ".php";

    if (file_exists($file)){
        require_once $file;
    }
    else {
        return null;
    }
});

final class AutoLoader
{

}