PHP error handler
=================

Common error handling with callbacks. Provides request url, environment dump for every error.

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

Uses set_error_handler, set_exception_handler and register_shutdown_function functions.

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
