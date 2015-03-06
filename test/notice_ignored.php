<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', php_sapi_name() === 'cli');
if (function_exists('xdebug_disable')) {
    xdebug_disable();
}

require __DIR__ . '/../src/ErrorHandler/ErrorHandler.php';

$ErrorHandler = new ErrorHandler\ErrorHandler();
$ErrorHandler->register();

function error()
{
    $a = array();
    echo $a[4];
}

error();
