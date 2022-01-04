<?php

namespace Aloefflerj\FedTheDog\Controller\Routes;

use Aloefflerj\FedTheDog\Controller\Helpers\UrlHelper;

class Routes
{
    use UrlHelper;

    private static $current;
    // public $post;
    // public $put;
    // public $delete;
    // public $options;
    private \Exception $error;
    public $routes;

    public function __construct()
    {
        $this->routes = [];
    }

    public function __get($name)
    {
        return $this->routes[$name];
    }

    public function add(): Routes
    {
        $currentUri = self::$current->name;
        $currentVerb = self::$current->verb;

        if (!array_key_exists($currentVerb, $this->routes)) {
            $this->routes[$currentVerb] = [];
        }

        if (!is_array($this->routes[$currentVerb])) {
            $this->routes[$currentVerb] = [];
        }

        if (array_key_exists($currentUri, $this->routes[$currentVerb])) {

            $this->error = new \Exception(
                " Error 409 => route \"{$currentUri}\" already exists => " .
                    " function \"" .
                    __FUNCTION__ .
                    "\" in " . __CLASS__ .
                    " line " . __LINE__,
                409
            );

            return $this;
        }

        $this->routes[$currentVerb][$currentUri] = self::$current;

        return $this;
    }

    public function get(string $uri, \closure $output, ?array $functionParams)
    {
        self::$current = new Get($uri, $output, $functionParams);

        return $this;
    }

    public function post(string $uri, \closure $output, ?array $functionParams)
    {
        self::$current = new Post($uri, $output, $functionParams);

        return $this;
    }

    public function getCurrent($currentUri)
    {
        $currentRoute = null;

        $requestMethod = $this->getRequestMethod();

        switch ($requestMethod) {
            case 'get':
                $currentRoute = (Get::getRoute($currentUri, $this->routes, $requestMethod));
                break;
            case 'post':
                $currentRoute = (Post::getRoute($currentUri, $this->routes, $requestMethod));
                break;
            default:
                echo "not get";
                break;
        }

        return $currentRoute;
    }

    public function getRouteByName($name)
    {
        $requestMethod = $this->getRequestMethod();

        $currentRoute = $this->routes[$requestMethod][$name];

        return $currentRoute;
    }

    public function dispatchRoute($currentRoute)
    {
        $requestMethod = $this->getRequestMethod();

        if (!$this->routeExists($currentRoute->name, $requestMethod)) {
            return false;
        }

        return $this->routes[$requestMethod][$currentRoute->name]->dispatch();
    }

    public function error()
    {
        return $this->error ?? null;
    }

    private function routeExists($currentRoute, $requestMethod)
    {
        if (!array_key_exists($currentRoute, $this->routes[$requestMethod])) {
            return false;
        }

        return true;
    }
}
