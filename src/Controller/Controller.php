<?php

namespace Aloefflerj\YetAnotherController\Controller;

use Aloefflerj\YetAnotherController\Controller\Helpers\UrlHelper;
use Aloefflerj\YetAnotherController\Controller\Http\Method;
use Aloefflerj\YetAnotherController\Controller\Http\Request;
use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use Aloefflerj\YetAnotherController\Controller\Http\Uri;
use Aloefflerj\YetAnotherController\Controller\Router\Route;
use Aloefflerj\YetAnotherController\Controller\Router\Router;
use Aloefflerj\YetAnotherController\Controller\Url\UrlHandler;
use Psr\Http\Message\RequestInterface;

class Controller
{
    use UrlHelper;

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
    }

    public function post(string $route, \closure $output, mixed $injectedParams = null): void
    {
        $uri = new Uri($this->baseUri . $route);
        $newRoute = new Route(
            $uri,
            Method::POST,
            $output,
            $injectedParams
        );

        $this->router->addRoute($newRoute);
    }

    public function dispatch(): void
    {
        $accessedRoute = $this->getMappedRoute();

        $request = $this->buildRequestFromRoute($accessedRoute);

        $accessedRoute->getOutput()($request);
    }

    private function getMappedRoute(): Route
    {
        /** @var Route $accessedRoute */
        $accessedRoute = $this->router->getRoutes()[$this->getAccessedMethod()->value][$this->getAccessedRoute()];
        return $accessedRoute;
    }

    private function buildRequestFromRoute(Route $route): RequestInterface
    {
        $stream = fopen('php://input', 'r');

        return new Request(
            $this->getAccessedMethod()?->value,
            $route->getUri(),
            new Stream($stream),
            getallheaders(),
            $_SERVER['SERVER_PROTOCOL']
        );
    }

    private function getAccessedRoute(): string
    {
        $urlHandler = new UrlHandler();
        return $urlHandler->getPath();
    }

    private function getAccessedMethod(): ?Method
    {
        $method = $this->getRequestMethod();
        $method = strtoupper($method);

        return Method::tryFrom($method);
    }
}