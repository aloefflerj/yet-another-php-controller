<?php

namespace Aloefflerj\YetAnotherController\Controller\Weird;

use Aloefflerj\YetAnotherController\Controller\Controller;
use Aloefflerj\YetAnotherController\Controller\Http\Response;
use Aloefflerj\YetAnotherController\Controller\Http\ServerRequest;

class WeirdController
{
    private Controller $controller;
    private $currentRoute;

    public function __construct(
        string $baseUri = ''
    ) {
        $this->controller = new Controller($baseUri);
    }

    public function route(string $route): self
    {
        $this->currentRoute = $route;

        return $this;
    }

    public function get(\closure ...$stack): self
    {
        $this->addRoute('get', ...$stack);
        return $this;
    }

    public function post(\closure ...$stack): self
    {
        $this->addRoute('post', ...$stack);
        return $this;
    }

    public function put(\closure ...$stack): self
    {
        $this->addRoute('put', ...$stack);
        return $this;
    }

    public function patch(\closure ...$stack): self
    {
        $this->addRoute('patch', ...$stack);
        return $this;
    }

    public function delete(\closure ...$stack): self
    {
        $this->addRoute('delete', ...$stack);
        return $this;
    }

    public function addRoute(string $method, \closure ...$stack): self
    {
        foreach ($stack as $executionStep) {
            $reflection = new \ReflectionFunction($executionStep);
            $arguments  = $reflection->getParameters();

            if (empty($arguments)) {
                $this->controller->$method(
                    $this->currentRoute,
                    function (ServerRequest $req, Response $res) use ($executionStep) {
                        $executionStep();
                    }
                );
                continue;
            }

            $preparedRoute = $this->currentRoute;
            $body = false;
            foreach ($arguments as $argument) {
                if ($argument->getName() === 'body') {
                    $body = true;
                    continue;
                };

                $preparedRoute .= "/{{$argument->getName()}}";
            }

            $this->controller->$method(
                $preparedRoute,
                function (ServerRequest $req, Response $res, \stdClass $args) use ($executionStep, $body) {
                    $args = (array)$args;
                    if ($body) {
                        $requestBodyContent = $req->getBody()->getContents();
                        $requestBody = json_decode($requestBodyContent);
                        $args[] = $requestBody;
                    }
                    $args = array_values($args);
                    $executionStep(...$args);
                }
            );
        }

        return $this;
    }

    public function dispatch(): void
    {
        $this->controller->dispatch();
    }
}
