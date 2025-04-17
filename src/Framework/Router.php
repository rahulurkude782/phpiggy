<?php

declare(strict_types=1);

namespace Framework;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    private array $errorHandler = [];

    /* Builds the routes array */
    public function add(string $method, string $path, array $controller): void
    {
        $path = $this->normalizePath($path);
        $regexPath = preg_replace('#{[^/]+}#', '([^/]+)', $path);
        $this->routes[] = [
            'path' => $path,
            'method' => strtoupper($method),
            'controller' => $controller,
            /* To add route specific middlewares. */
            'middlewares' => [],
            /* For route params. */
            'regexPath' => $regexPath
        ];
    }

    /* To normalize the path proper format eg: about/team/ ~ /about/team/ */
    private function normalizePath(string $path): string
    {
        $path = trim($path, '/');
        $path = "/{$path}/";
        $path = preg_replace('#[/]{2,}#', '/', $path);
        return $path;
    }

    /* Dispatches the route on be-half of incoming request action with given controller */
    public function dispatch(string $path, string $method, Container $container)
    {


        $path = $this->normalizePath($path);
        $method = strtoupper($_POST['_method'] ?? $method);

        foreach ($this->routes as $route) {
            if (!preg_match("#^{$route['regexPath']}$#", $path, $paramValues) || $route['method'] !== $method) {
                continue;
            }

            [$class, $function] = $route['controller'];

            $classInstance = $container ? $container->resolveDependency($class) : new $class();

            /* Extracting route params. */
            array_shift($paramValues);
            preg_match_all('#{([^/]+)}#', $route['path'], $paramKeys);
            $params = array_combine($paramKeys[1], $paramValues);

            /* Registerint middleware by chaining the actions. */
            $action = fn() => $classInstance->$function($params);

            $allMiddlewares = [...$route['middlewares'], ...$this->middlewares];

            foreach ($allMiddlewares as $middleware) {
                $middlewareInstance = $container ? $container->resolveDependency($middleware) : new $middleware();

                $action = fn() => $middlewareInstance->process($action);
            }

            return $action();
        }
        return $this->dispatchNotFound($container);
    }

    /* To add Global Middlewares. */
    public function addMiddleware(string $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function addRouteMiddleware(string $middleware): void
    {
        $lastRouteKey = array_key_last($this->routes);

        $this->routes[$lastRouteKey]['middlewares'][] = $middleware;
    }

    public function setErrorHandler(array $controller)
    {
        $this->errorHandler = $controller;
    }

    public function dispatchNotFound(Container $container)
    {

        [$controller, $function] = $this->errorHandler;

        $controllerInstance = $container ? $container->resolveDependency($controller) : new $controller();

        $action = fn() => $controllerInstance->$function();

        foreach ($this->middlewares as $middleware) {
            $middlewareInstance = $container ? $container->resolveDependency($middleware) : new $controller();
            $action = fn() => $middlewareInstance->process($action);
        }

        $action();
    }
}
