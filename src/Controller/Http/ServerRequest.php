<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Aloefflerj\YetAnotherController\Controller\Helpers\UriHelper;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ServerRequest extends Request #implements ServerRequestInterface
{
    private array $attributes = [];

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

    public function getParsedBody()
    {
        if ($this->isOfFormContentType()) {
            return (object)$_POST;
        }

        if ($this->isOfJsonContentType()) {
            return (object)json_decode($this->getBody());
        }

        throw new \Exception("There is currently no implementation to parse content of type '{$this->getHeader('Content-Type')[0]}'");
        return null;
    }

    public function withParsedBody($data)
    {
        $data = match (gettype($data)) {
            'object' => $this->parseObjectBody($data),
            'array' => $this->parseArrayBody($data),
            'null' => $data,
            default => throw new \InvalidArgumentException("Data to the parsed body should be an array, object or null")
        };

        $clone = clone $this->withBody($data);
        return $clone;
    }

    private function parseObjectBody(object $objectData): object
    {
        if ($this->isOfFormContentType()) {
            $arrayData = (array)$objectData;
            return $this->parseArrayBody($arrayData);
        }

        if ($this->isOfJsonContentType()) {
            return json_encode($objectData);
        }

        throw new \InvalidArgumentException("Data to the parsed body should be an array, object or null");
    }

    private function parseArrayBody(array $arrayData): object
    {
        if ($this->isOfFormContentType()) {
            foreach ($arrayData as $dataKey => $dataValue) {
                $_POST[$dataKey] = $dataValue;
            }
            return (object)$_POST;
        }

        if ($this->isOfJsonContentType()) {
            return json_encode($arrayData);
        }

        throw new \InvalidArgumentException("Data to the parsed body should be an array, object or null");
    }

    private function isOfFormContentType()
    {
        return in_array('application/x-www-form-urlencoded', $this->getHeader('Content-Type')) ||
            in_array('multipart/form-data', $this->getHeader('Content-Type'));
    }

    private function isOfJsonContentType()
    {
        return in_array('application/json', $this->getHeader('Content-Type'));
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    public function withAttribute($name, $value)
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    public function withoutAttribute($name)
    {
        $clone = clone $this;
        unset($clone->attributes[$name]);
        
        return $clone;
    }
}
