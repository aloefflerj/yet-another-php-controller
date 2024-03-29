<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController\Controller\Router;

use Aloefflerj\YetAnotherController\Controller\Http\Method;
use Psr\Http\Message\UriInterface;

class Route
{
    private \stdClass $params;

    public function __construct(
        private UriInterface $uri,
        private Method $method,
        private \closure $output
    ) {
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function setUri($uri): void
    {
        $this->uri = $uri;
    }

    public function getMethod(): Method
    {
        return $this->method;
    }

    public function setMethod($method): void
    {
        $this->method = $method;
    }

    public function getOutput(): \closure
    {
        return $this->output;
    }

    public function setOutput($output): void
    {
        $this->output = $output;
    }

    public function getParams(): \stdClass
    {
        return $this->params ?? new \stdClass();
    }

    public function setParams(\stdClass $params): void
    {
        $this->params = $params;
    }
}
