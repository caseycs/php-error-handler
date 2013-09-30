PHP error handler
=================

Common error handling with callbacks. Provides request url, environment dump for every error.

Usesset_error_handler, set_exception_handler and register_shutdown_function.

Basic usage:

```php
$ErrorHandler = new ErrorHandler\ErrorHandler;
$ErrorHandler->register();
```
