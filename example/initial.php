<?php
namespace mnhcc\yql\example;
{
    function autolad($class) {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        return require dirname(__DIR__).DIRECTORY_SEPARATOR.$path.'.class.php';
    }
    spl_autoload_register(__NAMESPACE__.'\\autolad');
}