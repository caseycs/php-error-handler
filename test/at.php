<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', php_sapi_name() === 'cli');

require __DIR__ . '/../src/ErrorHandler/ErrorHandler.php';

$ErrorHandler = new ErrorHandler\ErrorHandler();
$ErrorHandler->register();

function error()
{
    $a = array();
    echo @$a[4];
}

error();
