<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController\Controller\Router;

class Router
{
    public function __construct(private $routes = [])
    {
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function addRoute(Route $route): void
    {
        $this->routes[$route->getMethod()->value][$route->getUri()->getPath()] = $route;
    }
}
