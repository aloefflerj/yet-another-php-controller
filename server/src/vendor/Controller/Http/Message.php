<?php

namespace Aloefflerj\FedTheDog\Controller\Http;

use Aloefflerj\FedTheDog\Psr\Http\Message\MessageInterface;
use Aloefflerj\FedTheDog\Psr\Http\Message\StreamInterface;

// class Message
class Message implements MessageInterface
{

    private string $protocolVersion;

    private array $headers;

    private $body;

    public function __construct()
    {
        $this->protocolVersion = '1.0';
    }

    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion($version)
    {
        $clone = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        if (!empty($name) && array_key_exists($name, $this->headers)) {
            return true;
        }

        return false;
    }

    public function getHeader($name)
    {
        $name = strtolower($name);

        if (array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        }

        return [];
    }

    public function getHeaderLine($name)
    {
        $name = strtolower($name);

        if (array_key_exists($name, $this->headers)) {
            return is_array($this->headers[$name]) ? implode(', ', $this->headers[$name]) : $this->headers[$name];
        }

        return '';
    }

    public function withHeader($name, $value)
    {

        $name = strtolower($name);

        if (is_array($value)) {
            foreach ($value as $key => $argument) {
                $value[$key] = strtolower($argument);
            }
        } else {
            $value = strtolower($value);
        }

        // throw new \InvalidArgumentException();
        $clone = clone $this;

        $clone->headers[$name] = $value;

        return $clone;
    }
    
    public function withAddedHeader($name, $value)
    {
        $name = strtolower($name);

        if (is_array($value)) {
            foreach ($value as $key => $argument) {
                $value[$key] = strtolower($argument);
            }
        } else {
            $value = strtolower($value);
        }

        if(empty($this->headers[$name])) {
            $this->headers[$name] = $value;
        }else {
            $this->headers[$name][] = $value;
        }

        return $this;

    }

    public function withoutHeader($name)
    {
        $name = strtolower($name);

        if(empty($this->headers[$name])) {
            //throw new exception
        }

        $clone = clone $this;

        unset($clone->headers[$name]);

        return $clone;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $clone = clone $this;

        $clone->body = $body;

        return $clone;

    }
}
