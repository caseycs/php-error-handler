<?php
require 'bootstrap.php';

function error()
{
    throw new \Exception('exeption_message');
}

error();
