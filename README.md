# PHP error handler

Common error handling with callbacks. Provides custom error message with request url, 
referer, session and other environment info for every error.

Example:

```
SHUTDOWN Call to undefined function unexisted_function() in /Users/ikondrashov/github/php-error-handler/test/uncatchable.php:6
URL: localhost:3000/uncatchable.php
HTTP_REFERER: http://localhost:3000/uncatchable.php
SESSION: Array
(
    [a] => 5
)
POST: Array
(
    [b] => 10
)
COOKIES: Array
(
    [c] => 15
)
uniqid: 52496cfee1616
```

Installation via [Composer](http://getcomposer.org)

```
{
    "require": {
        "caseycs/php-error-handler": "dev-master",
    },
}

````

## Usage

Basic usage:

```php
$ErrorHandler = new ErrorHandler\ErrorHandler;
$ErrorHandler->register();
```

Advanced usage:

```php
if ($_SERVER['APPLICATION_ENV'] !== 'development') {
    $ErrorHandler = new ErrorHandler\ErrorHandler;
    $ErrorHandler->register();
    $ErrorHandler->addExceptionCallback(function () {header ('HTTP/1.0 500 Internal Server Error', true, 500);});
}
```

## Going deeper

First of all - make sure, that you have `error_log` value defined - both for cli and fpm (or apache) environments. Use `phpinfo()` for web and `php -i | grep error_log` for cli.
Make sure, that specified file is writeable for user, which executes your cli scripts (for example using crontab) and apache/fpm.

This is very important!

What's our goals?

For cli we are going to write all errors to common cli error log `/var/log/php-errors-cli.php` **and** to stderr of running script - 
for example from crontab `* * * * * php script.php >> script.log 2>&1`.

For cli we are going to write all errors to common web error log `/var/log/php-errors-fpm.php` **including** environment - url, referer, get, post, cookies, session etc.
Also we want to write environment for uncatchable errors - which are handled by `register_shutdown_function`.

## Drawbacks

Fatal errors, which are not handled by `set_error_handler` and are caught only by `register_shutdown_function`
appear in error log twice - first time as native php error, and second one - as our custom message with environment
info. Anybody known how to fix this?
