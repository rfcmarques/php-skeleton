<?php

namespace Core;

use Core\Middleware\Authorize;

class Router
{
    protected $routes = [];

    public function add(string $method, string $uri, string $action, array $middleware = []): void
    {
        [$controller, $controllerMethod] = explode('@', $action);

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod,
            'middleware' => $middleware,
        ];
    }

    public function get(string $uri, string $action, array $middleware = []): void
    {
        $this->add('GET', $uri, $action, $middleware);
    }

    public function post(string $uri, string $action, array $middleware = []): void
    {
        $this->add('POST', $uri, $action, $middleware);
    }

    public function put(string $uri, string $action, array $middleware = []): void
    {
        $this->add('PUT', $uri, $action, $middleware);
    }

    public function delete(string $uri, string $action, array $middleware = []): void
    {
        $this->add('DELETE', $uri, $action, $middleware);
    }

    public function dispatch(string $uri): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper((string) $_POST['_method']);
        }

        if ($requestMethod === 'HEAD') {
            $requestMethod = 'GET';
        }

        $path = '/' . trim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) continue;

            $params = [];

            if ($this->match($path, $route['uri'], $params)) {
                foreach ($route['middleware'] as $middleware) {
                    (new Authorize())->handle($middleware);
                }

                $controller = 'App\\Controllers\\' . $route['controller'];
                $controllerMethod = $route['controllerMethod'];

                $controllerInstance = new $controller();
                $controllerInstance->$controllerMethod($params);
                return;
            }
        }

        if (class_exists('App\\Controllers\\ErrorController') && method_exists('App\\Controllers\\ErrorController', 'notFound')) {
            \App\Controllers\ErrorController::notFound();
            return;
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    protected function match(string $path, string $routeUri, array &$params): bool
    {
        $uriSegments = $path === '/' ? [] : explode('/', trim($path, '/'));
        $routeSegments = $routeUri   === '/' ? [] : explode('/', trim($routeUri, '/'));

        if (count($uriSegments) !== count($routeSegments)) {
            return false;
        }

        $params = [];
        foreach ($routeSegments as $i => $segment) {
            if (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $segment, $m)) {
                $params[$m[1]] = $uriSegments[$i];
                continue;
            }

            if ($segment !== $uriSegments[$i]) {
                return false;
            }
        }

        return true;
    }
}
