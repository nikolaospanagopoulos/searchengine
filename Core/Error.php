<?php


namespace Core;


class Error
{




    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    public static function exceptionHandler($exception)
    {

        if (\App\Config::SHOW_ERRORS) {
            echo '<h1>Fatal error</h1>';
            echo '<p>uncaught exception' . get_class($exception) . ' </p>';
            echo '<p>Message:' . $exception->getMessage() . '</p>';
            echo '<p>Stack trace: <pre>' . $exception->getTraceAsString() . '</pre></p>';
            echo '<p>Thrown in ' . $exception->getFile() . '</p>';
        } else {
            $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
            ini_set('error_log', $log);

            $message = "uncaught exception" . get_class($exception);
            $message .= "Message:" . $exception->getMessage();
            $message .= 'Stack trace: ' . $exception->getTraceAsString();
            $message .= 'Thrown in ' . $exception->getFile();
            error_log($message);
            echo '<h1>An error occured</h1>';
        }
    }
}
