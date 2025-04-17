<?php

declare(strict_types=1);

namespace Framework;

use App\Config\Paths;

class App
{
    private Router $router;
    private Container $container;

    public function __construct(string $containerDefinitionsPath)
    {
        $this->router = new Router();
        $this->container = new Container();

        /* For outsourcing the container definitions */
        if ($containerDefinitionsPath) {
            $containerDefinition = include Paths::SOURCE . $containerDefinitionsPath;
            $this->container->addDefinitions($containerDefinition);
        }
    }

    public function run(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        $this->router->dispatch($path, $method, $this->container);
    }

    /* HTTP Methods Declarations. */

    public function get(string $path, array $controller): App
    {
        $this->router->add('GET', $path, $controller);
        return $this;
    }
    public function post(string $path, array $controller): App
    {
        $this->router->add('POST', $path, $controller);
        return $this;
    }
    public function delete(string $path, array $controller): App
    {
        $this->router->add('DELETE', $path, $controller);
        return $this;
    }

    public function addMiddleware(string $middleware): void
    {
        $this->router->addMiddleware($middleware);
    }

    public function addRouteMiddleware(string $middleware): void
    {
        $this->router->addRouteMiddleware($middleware);
    }

    public function setErrorHandler(array $controller)
    {
        $this->router->setErrorHandler($controller);
    }
}
