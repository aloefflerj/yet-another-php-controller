<?php

namespace Aloefflerj\YetAnotherController\Controller;

use Aloefflerj\YetAnotherController\Controller\Config\ControllerConfigSubitter;
use Aloefflerj\YetAnotherController\Controller\Config\ControllerSetup;
use Aloefflerj\YetAnotherController\Controller\Exceptions\MoreThanOneRouteWasFound;
use Aloefflerj\YetAnotherController\Controller\Exceptions\OutputReturnMustBeAResponse;
use Aloefflerj\YetAnotherController\Controller\Exceptions\RouteNotFound;
use Aloefflerj\YetAnotherController\Controller\Helpers\UrlHelper;
use Aloefflerj\YetAnotherController\Controller\Http\Method;
use Aloefflerj\YetAnotherController\Controller\Http\Request;
use Aloefflerj\YetAnotherController\Controller\Http\Response;
use Aloefflerj\YetAnotherController\Controller\Http\ServerRequest;
use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use Aloefflerj\YetAnotherController\Controller\Http\StreamBuilder;
use Aloefflerj\YetAnotherController\Controller\Http\Uri;
use Aloefflerj\YetAnotherController\Controller\Router\Route;
use Aloefflerj\YetAnotherController\Controller\Router\Router;
use Aloefflerj\YetAnotherController\Controller\Url\UrlHandler;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Controller
{
    use UrlHelper;

    public function __construct(
        private string $baseUri = '',
        private Router $router = new Router([]),
        private ControllerSetup $setup = new ControllerSetup(),
        private StreamBuilder $streamBuilder = new StreamBuilder()
    ) {
    }

    public function get(string $route, \closure $output): void
    {
        $uri = new Uri($this->baseUri . $route);
        $newRoute = new Route(
            $uri,
            Method::GET,
            $output
        );

        $this->router->addRoute($newRoute);
    }

    public function post(string $route, \closure $output): void
    {
        $uri = new Uri($this->baseUri . $route);
        $newRoute = new Route(
            $uri,
            Method::POST,
            $output
        );

        $this->router->addRoute($newRoute);
    }

    public function put(string $route, \closure $output): void
    {
        $uri = new Uri($this->baseUri . $route);
        $newRoute = new Route(
            $uri,
            Method::PUT,
            $output
        );

        $this->router->addRoute($newRoute);
    }

    public function patch(string $route, \closure $output): void
    {
        $uri = new Uri($this->baseUri . $route);
        $newRoute = new Route(
            $uri,
            Method::PATCH,
            $output
        );

        $this->router->addRoute($newRoute);
    }

    public function delete(string $route, \closure $output): void
    {
        $uri = new Uri($this->baseUri . $route);
        $newRoute = new Route(
            $uri,
            Method::DELETE,
            $output
        );

        $this->router->addRoute($newRoute);
    }

    public function dispatch(): void
    {
        $accessedRoute = $this->getMappedRoute();

        $request = $this->buildRequestFromRoute($accessedRoute);
        $response = $this->buildResponse($accessedRoute);

        /** @var ResponseInterface $response */
        $response = $accessedRoute->getOutput()($request, $response, $accessedRoute->getParams());

        if (is_null($response))
            return;

        if (!is_a($response, ResponseInterface::class)) {
            throw new OutputReturnMustBeAResponse('Output closure must return an implementation of ' . ResponseInterface::class);
        }

        $this->setup->submitHeaders($response);

        echo $response->getBody();
    }

    private function getMappedRoute(): Route
    {
        /** @var Route[] $routes */
        $routes = $this->router->getRoutes()[$this->getAccessedMethod()->value];

        $foundRoutes = [];
        foreach ($routes as $routeNaming => $route) {
            $regexRoute = $this->buildRegexRoute($routeNaming);
            $url = $this->getAccessedRoute();

            if (!preg_match($regexRoute, $url)) {
                continue;
            }

            preg_match($regexRoute, $url, $matches);

            $foundParams = array_filter(
                $matches,
                fn (string|int $key) => !is_int($key),
                ARRAY_FILTER_USE_KEY
            );

            $route->setParams((object)$foundParams);
            $foundRoutes[] = $route;
        }

        if (empty($foundRoutes)) {
            throw new RouteNotFound($url);
        }

        if (count($foundRoutes) > 1) {
            throw new MoreThanOneRouteWasFound($foundRoutes, $url);
        }

        $foundRoute = $foundRoutes[0];

        return $foundRoute;
    }

    private function buildRequestFromRoute(Route $route): RequestInterface
    {
        return new ServerRequest(
            $this->getAccessedMethod()?->value,
            $route->getUri(),
            $this->streamBuilder->buildStreamFromPHPInput(),
            getallheaders(),
            $_SERVER['SERVER_PROTOCOL']
        );
    }

    private function buildResponse(): ResponseInterface
    {
        return new Response(
            http_response_code(),
            [],
            Stream::buildFromString(''),
            $_SERVER['SERVER_PROTOCOL'],
            ''
        );
    }

    private function getAccessedRoute(): string
    {
        $urlHandler = new UrlHandler();
        return $urlHandler->getPath();
    }

    private function getAccessedMethod(): Method
    {
        $method = $this->getRequestMethod();
        $method = strtoupper($method);

        return Method::try($method);
    }

    private function buildRegexRoute(string $routeNaming): string
    {
        $regexRoute = preg_replace("/\{(.*?)\}/", '(?<$1>[^/]+?)', $routeNaming);

        return "#^{$regexRoute}$#";
    }
}
