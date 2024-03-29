<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class Message implements MessageInterface
{
    use Headers;

    public function __construct(
        protected array $headers = [],
        protected string $protocolVersion = '1.0',
        protected StreamInterface | string $body = ''
    ) {
        if (is_string($body))
            $body = Stream::buildFromString($body);

        $this->body = $body;
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

    public function withHeader($name, $value): static
    {
        $name = strtolower($name);
        if (!in_array($name, $this->getValidHeaders())) {
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

        $clone = clone $this;

        if (!isset(($this->headers[$name]))) {
            return $clone;
        }

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
