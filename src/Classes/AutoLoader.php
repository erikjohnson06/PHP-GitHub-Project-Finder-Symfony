<?php

namespace App\Classes;

spl_autoload_register(function($class){
    
    //$exp = explode("\\", $class);

    //$path = JPATH_BASE . DS . 'administrator' . DS . 'custom' . DS . 'classes' . DS . implode(DS, $exp) . '.php';

    $class = str_replace("\\", DIRECTORY_SEPARATOR, $class);

    $class = preg_replace("/^App/", "src", $class);
    $file = realpath(__DIR__) . DIRECTORY_SEPARATOR . $class . ".php";
    $file = $class . ".php";

    $log = "\r\n " . date("m-d-y h:i:s A") . __FILE__ . ": " . __FUNCTION__ . ": " . __LINE__ . " - class: " . $file;
    file_put_contents("testlog.txt", $log, FILE_APPEND);

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