<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message #implements RequestInterface
{
    private Method $method;
    
    public function __construct(
        string | Method $method,
        private string | UriInterface $uri = '',
        private string | StreamInterface $stream = '',
        private array $headers = [],
        private string $version = '1.1'
    ) {
        $this->setMethod($method);
        $this->uri = $uri;
        $this->stream = $stream;
        $this->headers = $headers;
        $this->version = $version;
    }

    private function setMethod(string | Method $method): void
    {
        $this->assertMethod($method);

        if (is_string($method)) {
            $method = Method::tryFrom($method);    
        }
        
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method->value;
    }

    private function assertMethod(string | Method $method): void
    {
        if (is_a($method, Method::class)) {
            return;
        }

        $method = trim($method);
        $method = strtoupper($method);
        
        if (!in_array($method, Method::getAllPossibleValues())) {
            throw new \Exception("Method '{$method}' is not a valid http method.");
        }
    }
}
