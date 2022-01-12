<?php

namespace Aloefflerj\YetAnotherController\Controller;

use Aloefflerj\YetAnotherController\Controller\Helpers\StringHelper;
use Aloefflerj\YetAnotherController\Controller\Helpers\UrlHelper;
use Aloefflerj\YetAnotherController\Controller\Routes\Routes;
use Aloefflerj\YetAnotherController\Controller\Url\UrlHandler;

class BaseController
// class BaseController implements ControllerInterface
{
    use StringHelper;
    use UrlHelper;

    public Routes $routes;
    private array $data;
    private UrlHandler $urlHandler;
    private \Exception $error;

    public function __construct()
    {
        $this->urlHandler = new UrlHandler();
        $this->routes = new Routes();
    }

    public function get(string $uri, \closure $output, ?array $functionParams = null): BaseController
    {
        $routes = $this->routes->get($uri, $output, $functionParams)->add();
        if ($routes->error()) {
            $this->error = $routes->error();
        }
        return $this;
    }

    public function post(string $uri, \closure $output, ?array $functionParams = null): BaseController
    {
        $routes = $this->routes->post($uri, $output, $functionParams)->add();
        if ($routes->error()) {
            $this->error = $routes->error();
        }
        return $this;
    }

    public function put(string $uri, \closure $output, ?array $functionParams = null): BaseController
    {
        $routes = $this->routes->put($uri, $output, $functionParams)->add();
        if ($routes->error()) {
            $this->error = $routes->error();
        }
        return $this;
    }

    public function delete(string $uri, \closure $output, ?array $functionParams = null): BaseController
    {
        $routes = $this->routes->delete($uri, $output, $functionParams)->add();
        if ($routes->error()) {
            $this->error = $routes->error();
        }
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function dispatch(): BaseController
    {
        if ($this->error()) {
            echo $this->error()->getMessage();
            die();
        }

        $currentRouteName = $this->getCurrentRouteName();

        // Check if it is mapped
        if (!$this->routeExists($currentRouteName)) {
            $this->error = new \Exception("Error 404", 404);
            return $this;
        }

        $currentRoute = $this->routes->getRouteByName($currentRouteName);

        $dispatch = $this->routes->dispatchRoute($currentRoute);
        if (!$dispatch) {
            $this->error = new \Exception("Error 405", 405);
            return $this;
        }

        return $this;
    }

    public function error(): ?\Exception
    {
        return $this->error ?? null;
    }

    /**
     * ||================================================================||
     *                          HELPER FUNCTIONS
     * ||================================================================||
     * 
     */

    /**
     * @return Routes[]|null
     */
    public function getRoutes()
    {
        return $this->routes ?? null;
    }

    private function getCurrentRouteName(): ?string
    {
        $currentUri = $this->urlHandler->getUriPath();

        $currentRoute = $this->routes->getCurrent($currentUri);

        return $currentRoute;
    }

    private function routeExists(string $currentRoute): bool
    {
        $requestMethod = $this->getRequestMethod();
        return array_key_exists($currentRoute, $this->routes->$requestMethod);
    }
}
