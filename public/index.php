<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Csrf;
use App\Database;
use App\Router;
use App\Session;
use App\View;

$dotenv = parse_ini_file(__DIR__ . '/../.env');
if ($dotenv) {
    foreach ($dotenv as $key => $value) {
        $_ENV[$key] = $value;
    }
}

$config = require __DIR__ . '/../config/app.php';

Database::setConfig($config['database']);
Session::setConfig($config['session']);
Csrf::setTokenName($config['security']['csrf_token_name']);

Session::start();

View::share('config', $config);

$router = new Router();

$router->get('/', 'App\\Controllers\\PhotoController@index');
$router->get('/photos', 'App\\Controllers\\PhotoController@index');
$router->get('/photos/{id}', 'App\\Controllers\\PhotoController@show');
$router->get('/photos/create', 'App\\Controllers\\PhotoController@create');
$router->post('/photos', 'App\\Controllers\\PhotoController@store', ['App\\Middleware\\CsrfMiddleware']);
$router->get('/photos/{id}/edit', 'App\\Controllers\\PhotoController@edit');
$router->post('/photos/{id}/update', 'App\\Controllers\\PhotoController@update', ['App\\Middleware\\CsrfMiddleware']);
$router->post('/photos/{id}/delete', 'App\\Controllers\\PhotoController@destroy', ['App\\Middleware\\CsrfMiddleware']);

$router->get('/login', 'App\\Controllers\\AuthController@showLogin');
$router->post('/login', 'App\\Controllers\\AuthController@login', ['App\\Middleware\\CsrfMiddleware']);
$router->get('/register', 'App\\Controllers\\AuthController@showRegister');
$router->post('/register', 'App\\Controllers\\AuthController@register', ['App\\Middleware\\CsrfMiddleware']);
$router->get('/logout', 'App\\Controllers\\AuthController@logout');

$router->get('/upload', 'App\\Controllers\\PhotoController@create');

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

try {
    $router->dispatch($method, $path);
} catch (Exception $e) {
    if ($config['debug']) {
        echo '<h1>Error</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        http_response_code(500);
        echo 'Internal Server Error';
    }
}