<?php

declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');

define('APP_PATH', dirname(__DIR__));

require APP_PATH . '/vendor/autoload.php';

use Core\Router;
use Core\Session;

Session::start();

$router = new Router();

$routes = require basePath('routes.php');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($uri);
