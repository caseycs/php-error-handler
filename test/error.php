<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', php_sapi_name() === 'cli');
if (function_exists('xdebug_disable')) {
    xdebug_disable();
}

require __DIR__ . '/../src/ErrorHandler/ErrorHandler.php';

$ErrorHandler = new ErrorHandler\ErrorHandler();
$ErrorHandler->register();

function error()
{
    echo $unexisted_variable;
}

error();
