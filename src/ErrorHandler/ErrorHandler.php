<?php
namespace ErrorHandler;

class ErrorHandler
{

    /**
     * @var \Closure[]
     */
    private $callbacks_error = array();

    /**
     * @var \Closure[]
     */
    private $callbacks_exception = array();

    private $uniqid;

    /**
     * Зарегистрировать автоподгрузчик
     */
    public function register()
    {
        set_error_handler(array($this, 'handleError'), E_ALL | E_STRICT);
        set_exception_handler(array($this, 'handleException'));
        register_shutdown_function(array($this, 'handleShutdown'));
    }

    public function addErrorCallback(\Closure $callback)
    {
        $this->callbacks_error[] = $callback;
    }

    public function addExceptionCallback(\Closure $callback)
    {
        $this->callbacks_exception[] = $callback;
    }

    public function handleError($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if (!error_reporting()) {
            //однако собака (@), ничего не делаем
            return;
        }

        switch ($errno) {
            case E_ERROR:
                $errno_str = 'E_ERROR';
                break;
            case E_WARNING:
                $errno_str = 'E_WARNING';
                break;
            case E_PARSE:
                $errno_str = 'E_PARSE';
                break;
            case E_NOTICE:
                $errno_str = 'E_NOTICE';
                break;
            case E_CORE_ERROR:
                $errno_str = 'E_CORE_ERROR';
                break;
            case E_CORE_WARNING:
                $errno_str = 'E_CORE_WARNING';
                break;
            case E_COMPILE_ERROR:
                $errno_str = 'E_COMPILE_ERROR';
                break;
            case E_COMPILE_WARNING:
                $errno_str = 'E_COMPILE_WARNING ';
                break;
            case E_USER_ERROR  :
                $errno_str = 'E_USER_ERROR';
                break;
            case E_USER_WARNING:
                $errno_str = 'E_USER_WARNING';
                break;
            case E_USER_NOTICE:
                $errno_str = 'E_USER_NOTICE';
                break;
            case E_STRICT:
                $errno_str = 'E_STRICT';
                break;
            case E_DEPRECATED:
                $errno_str = 'E_DEPRECATED';
                break;
            case E_USER_DEPRECATED:
                $errno_str = 'E_USER_DEPRECATED';
                break;
            default:
                $errno_str = 'UNKNOWN';
        }

        $message = $errno_str . ' [' . $errno . '] ' . $errstr . " in {$errfile}:{$errline}\n";

        $Exception = new \Exception();
        $exception_trace = $Exception->getTraceAsString();
        $exception_trace = substr($exception_trace, strpos($exception_trace, "\n") + 1);

        $message .= $exception_trace;
        if ($environment = $this->environmentToString()) {
            $message .= "\n" . $environment;
        }

        $this->save($message);

        foreach ($this->callbacks_error as $callback) {
            $callback($message);
        }
    }

    public function handleShutdown()
    {
        $error = error_get_last();
        if ($error !== null) {
            $this->handleError(E_CORE_ERROR, $error['message'], $error['file'], $error['line'], null);
        }
    }

    public function handleException(\Exception $Exception)
    {
        $message = 'EXCEPTION ' . get_class($Exception) . ' ' . $Exception->getMessage() . " in {$Exception->getFile()}:{$Exception->getLine()}\n";
        $message .= $Exception->getTraceAsString();
        $message .= $this->environmentToString();

        $this->save($message);

        foreach ($this->callbacks_exception as $callback) {
            $callback($message);
        }
    }

    /**
     * Запрошенная страница
     *
     * @return string
     */
    private function environmentToString()
    {
        $log = '';

        if (isset ($_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'], $_SERVER['REQUEST_URI'])) {
            $log .= "URL: " . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'] . "\n";
        }

        if (!empty ($_SERVER['HTTP_REFERER'])) {
            $log .= 'HTTP_REFERER: ' . $_SERVER['HTTP_REFERER'] . "\n";
        }

        //post/cookies/session/files
        if (!empty ($_POST)) {
            $log .= '$_POST: ' . print_r($_POST, true);
        }
        if (!empty ($_FILES)) {
            $log .= '$_FILES: ' . print_r($_FILES, true);
        }
        if (!empty ($_COOKIE)) {
            $log .= '$_COOKIE: ' . print_r($_COOKIE, true);
        }
        if (!empty ($_SESSION)) {
            $log .= '$_SESSION: ' . print_r($_SESSION, true);
        }

        if (php_sapi_name() !== 'cli') {
            $log .= "\nuniqid: " . $this->uniqid();
        }

        return $log;
    }

    private function save($message)
    {
        error_log($message);
    }

    private function uniqid()
    {
        if ($this->uniqid === null) {
            $this->uniqid = uniqid();
        }
        return $this->uniqid;
    }
}
