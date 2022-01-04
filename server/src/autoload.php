<?php

function autoload($class){

    $prefix = 'Aloefflerj\\FedTheDog\\';

    $baseDir = __DIR__ . '/vendor/';

    $len = strlen($prefix);

    if(strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);

    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if(file_exists($file)) {
        require $file;
    }
};

spl_autoload_register('autoload');