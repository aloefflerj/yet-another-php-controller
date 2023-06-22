<?php

namespace Aloefflerj\YetAnotherController\Controller;

use Aloefflerj\YetAnotherController\Controller\Http\Method;
use Aloefflerj\YetAnotherController\Controller\Http\Uri;
use Aloefflerj\YetAnotherController\Controller\Router\Route;
use Aloefflerj\YetAnotherController\Controller\Router\Router;

class Controller
{
    public function __construct(
        private string $baseUri = '',
        private Router $router = new Router([])
    ) {
    }

    public function get(string $route, \closure $output, mixed $injectedParams = null): void
    {
        $uri = new Uri($this->baseUri . $route);
        $newRoute = new Route(
            $uri,
            Method::GET,
            $output,
            $injectedParams
        );

        $this->router->addRoute($newRoute);
        dd($this->router->getRoutes());
    }
}