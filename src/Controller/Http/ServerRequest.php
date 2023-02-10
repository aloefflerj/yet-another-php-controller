<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Aloefflerj\YetAnotherController\Controller\Helpers\UriHelper;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ServerRequest extends Request #implements ServerRequestInterface
{
    public function getServerParams()
    {
        return $_SERVER;
    }

    public function getCookieParams()
    {
        return $_COOKIE;
    }

    public function withCookieParams(array $cookies)
    {
        $_COOKIE = $cookies;
        $clone = clone $this;
        return $clone;
    }
}