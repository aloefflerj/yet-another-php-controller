<?php

namespace Aloefflerj\YetAnotherController\Controller\Routes;

use Aloefflerj\YetAnotherController\Controller\Helpers\HttpHelper;
use Aloefflerj\YetAnotherController\Controller\Helpers\UrlHelper;

class Routes
{
    use UrlHelper;
    use HttpHelper;

    private static $current;
    private \Exception $error;
    public $routes;
    private array $httpMethods;

    public function __construct()
    {
        $this->routes = [];
    }

    public function __get($name)
    {
        return $this->routes[$name];
    }

    public function __call($httpMethod, $params)
    {
        $this->httpMethods = $this->getHttpMethods();

        if (!in_array($httpMethod, $this->httpMethods)) {
            throw new \Exception('Call to undefined http method');
        }

        if (count($params) !== 3) {
            throw new \ArgumentCountError();
            return;
        }

        [$uri, $output, $functionParams] = $params;

        if (!is_string($uri)) {
            throw new \TypeError(
                $this->typeErrorMsg($httpMethod, 'string', 1, $uri)
            );
            return;
        }

        if (!is_callable($output)) {
            throw new \TypeError(
                $this->typeErrorMsg($httpMethod, 'closure', 2, $output)
            );
            return;
        }

        if ($functionParams && !is_array($functionParams)) {
            throw new \TypeError(
                $this->typeErrorMsg($httpMethod, 'array', 3, $functionParams)
            );
            return;
        }

        $httpMethodClass = __NAMESPACE__ . '\\' . ucfirst($httpMethod);
        self::$current = new $httpMethodClass($uri, $output, $functionParams);

        return $this;
    }

    public function add(): Routes
    {
        $currentUri = self::$current->name;
        $currentMethod = self::$current->method;

        if (!array_key_exists($currentMethod, $this->routes)) {
            $this->routes[$currentMethod] = [];
        }

        if (!is_array($this->routes[$currentMethod])) {
            $this->routes[$currentMethod] = [];
        }

        if (array_key_exists($currentUri, $this->routes[$currentMethod])) {

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

        $this->routes[$currentMethod][$currentUri] = self::$current;

        return $this;
    }

    public function getCurrent(string $currentUri): string
    {
        $currentRoute = null;

        $requestMethod = $this->getRequestMethod();

        $requestMethodClass = __NAMESPACE__ . '\\' . ucfirst($requestMethod);

        $currentRoute = $requestMethodClass::getRoute($currentUri, $this->routes, $requestMethod);

        return $currentRoute;
    }

    public function getRouteByName(string $name): Route
    {
        $requestMethod = $this->getRequestMethod();

        $currentRoute = $this->routes[$requestMethod][$name];

        return $currentRoute;
    }

    public function dispatchRoute(Route $currentRoute): Route
    {
        $requestMethod = $this->getRequestMethod();

        if (!$this->routeExists($currentRoute->name, $requestMethod)) {
            return false;
        }

        return $this->routes[$requestMethod][$currentRoute->name]->dispatch();
    }

    private function routeExists(string $currentRoute, string $requestMethod): bool
    {
        if (!array_key_exists($currentRoute, $this->routes[$requestMethod])) {
            return false;
        }

        return true;
    }

    public function error(): ?\Exception
    {
        return $this->error ?? null;
    }

    /**
     * @param string $name
     * @param string $type
     * @param mixed $param
     * @return string
     */
    private function typeErrorMsg(string $functionName, string $type, int $argNumber, $param): string
    {
        $class = __CLASS__;
        $wrongType = gettype($param);

        return <<<ERROR
            Argument $argNumber passed to $class::$functionName() must be of the type $type, $wrongType given;
        ERROR;
    }
}
