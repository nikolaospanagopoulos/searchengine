<?php




spl_autoload_register(function ($class) {
    $root = dirname(__DIR__);
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_readable($file)) {

        require $root . '/' . str_replace('\\', '/', $class) . '.php';
    }
});


error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


$router = new Core\Router;
$router->add("", ['controller' => "Home", 'action' => 'index']);
$router->add("analytics", ['controller' => "Analytics", 'action' => 'analyticsForm']);
$router->add("getanalytics", ['controller' => "Analytics", 'action' => 'showAnalytics']);
$router->add("imagesSearch", ['controller' => "Home", 'action' => 'images']);
$router->add("search/{id:\d+}", ['controller' => "Search", 'action' => 'search']);
$router->add("imagesearch/{id:\d+}", ['controller' => "Search", 'action' => 'imagesSearch']);

$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);
$router->dispatch($_SERVER['QUERY_STRING']);

