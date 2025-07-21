<?php

declare(strict_types=1);

namespace App;

class Router
{
    private array $routes = [];
    private array $middleware = [];

    public function get(string $path, string $handler, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, string $handler, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, string $handler, array $middleware = []): self
    {
        return $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, string $handler, array $middleware = []): self
    {
        return $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, string $handler, array $middleware): self
    {
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];

        return $this;
    }

    public function dispatch(string $method, string $path): void
    {
        $route = $this->findRoute($method, $path);

        if ($route === null) {
            http_response_code(404);
            echo 'Page not found';

            return;
        }

        $this->executeMiddleware($route['middleware']);
        $this->executeHandler($route['handler'], $route['params'] ?? []);
    }

    private function findRoute(string $method, string $path): ?array
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $routePath => $route) {
            if ($routePath === $path) {
                return $route;
            }

            $params = $this->matchParameterizedRoute($routePath, $path);
            if ($params !== null) {
                $route['params'] = $params;

                return $route;
            }
        }

        return null;
    }

    private function matchParameterizedRoute(string $routePath, string $actualPath): ?array
    {
        $routePattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $routePattern = '#^' . $routePattern . '$#';

        if (!preg_match($routePattern, $actualPath, $matches)) {
            return null;
        }

        array_shift($matches);
        preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames);

        return array_combine($paramNames[1], $matches);
    }

    private function executeMiddleware(array $middleware): void
    {
        foreach ($middleware as $middlewareClass) {
            if (class_exists($middlewareClass)) {
                $instance = new $middlewareClass();
                $instance->handle();
            }
        }
    }

    private function executeHandler(string $handler, array $params = []): void
    {
        if (str_contains($handler, '@')) {
            [$controllerClass, $method] = explode('@', $handler, 2);

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $method)) {
                    call_user_func_array([$controller, $method], array_values($params));

                    return;
                }
            }
        }

        if (is_callable($handler)) {
            call_user_func_array($handler, array_values($params));

            return;
        }

        throw new \RuntimeException("Handler not found: {$handler}");
    }
}