<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Aloefflerj\YetAnotherController\Controller\Helpers\UriHelper;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message implements RequestInterface
{
    use UriHelper;

    protected Method $method;
    protected UriInterface $uri;
    protected string $requestTarget;

    public function __construct(
        string | Method $method,
        string | UriInterface $uri = '',
        StreamInterface $body = new Stream(),
        array $headers = [],
        string $protocolVersion = '1.1'
    ) {
        $this->setMethod($method);
        $this->setUri($uri);
        $this->body = $body;
        $this->headers = $headers;
        $this->protocolVersion = $protocolVersion;
        $this->requestTarget = strval($this->uri);
    }

    public function getMethod()
    {
        return $this->method->value;
    }

    public function withMethod($method)
    {
        $clone = clone $this;
        $clone->setMethod($method);

        return $clone;
    }

    private function setMethod(string | Method $method): void
    {
        $this->assertMethod($method);

        if (is_string($method)) {
            $method = trim($method);
            $method = strtoupper($method);
            $method = Method::tryFrom($method);
        }

        $this->method = $method;
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

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $clone = clone $this;

        $toBePreservedUriHost = $this->uri->getHost();
        if ($preserveHost && !empty($toBePreservedUriHost)) {
            $uri = $uri->withHost($toBePreservedUriHost);
        }

        $clone->setUri($uri);

        return $clone;
    }

    private function setUri(string | UriInterface $uri): void
    {
        if (is_string($uri)) {
            $uri = new Uri($uri);
        }

        $this->uri = $uri;
    }

    public function getRequestTarget()
    {
        $requestTarget = $this->requestTarget;
        if (!preg_match('/^.*\/\/.*\/$/', $requestTarget)) {
            return $requestTarget;
        }
        return rtrim($requestTarget, '/');
    }

    public function withRequestTarget($requestTarget)
    {
        $clone = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
    }
}
