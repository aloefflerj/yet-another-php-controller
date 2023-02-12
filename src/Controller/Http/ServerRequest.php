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

    public function getQueryParams()
    {
        $queryParams = $this->getUri()->getQuery();
        if (empty($queryParams))
            return [];

        preg_match_all('/([^[\?|&]+)=([^&]*)/', $queryParams, $queryParamsMatches);
        $queryParamsMatches = $queryParamsMatches[0];
        
        $formattedQueryParams = [];
        foreach ($queryParamsMatches as $queryKeyAndValue) {
            [$queryKey, $queryValue] = explode('=', $queryKeyAndValue);
            $formattedQueryParams[$queryKey] = $queryValue;
        }

        return $formattedQueryParams;
    }

    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        if (empty($query)) {
            return $clone;
        }

        $queryString = '';
        $firstQuery = true;
        foreach ($query as $queryKey => $queryValue) {
            
            if ($firstQuery) {
                $queryString .= "?{$queryKey}={$queryValue}";
                $firstQuery = false;
                continue;
            }

            $queryString .= "&{$queryKey}={$queryValue}";
        }
        
        $clone->uri = $clone->uri->withQuery($queryString);
        return $clone;
    }
}
