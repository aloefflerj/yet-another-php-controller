<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Aloefflerj\YetAnotherController\Controller\PSR\MessageInterface;
use Aloefflerj\YetAnotherController\Controller\PSR\StreamInterface;

class Message implements MessageInterface
{
    use Headers;

    /**
     * @var string|array[]
     */
    private $headers;
    private string $protocolVersion;
    private $body;

    public function __construct()
    {
        $this->protocolVersion = '1.0';
        $this->headers = [];
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @param string $version
     * @return static
     */
    public function withProtocolVersion($version): static
    {
        $clone = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name): bool
    {
        if (empty($name) || !array_key_exists($name, $this->headers)) {
            return false;
        }

        return true;
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
            return is_array($this->headers[$name]) ? implode(',', $this->headers[$name]) : $this->headers[$name];
        }

        return '';
    }

    /**
     * @param string $name
     * @param string|string[] $value
     * @throws \InvalidArgumentException
     * @return self
     */
    public function withHeader($name, $value): static
    {
        if (!in_array(strtolower($name), $this->getValidHeaders())) {
            throw new \InvalidArgumentException('This header name does not exists');
        }

        $clone = clone $this;

        if (!is_array($value))
            $value = [$value];

        $clone->headers[$name] = $value;
        return $clone;
    }

    public function withAddedHeader($name, $value)
    {
        if (!in_array(strtolower($name), $this->getValidHeaders())) {
            throw new \InvalidArgumentException('This header name does not exists');
        }

        $clone = clone $this;

        if (!isset($this->headers[$name])) {
            return $clone->withHeader($name, $value);
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $clone->headers[$name] = array_merge($clone->headers[$name], $value);

        return $clone;
    }

    public function withoutHeader($name)
    {
        $name = strtolower($name);

        if (empty($this->headers[$name])) {
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
