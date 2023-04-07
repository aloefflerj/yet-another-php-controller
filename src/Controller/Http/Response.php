<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response extends Message #implements ResponseInterface
{
    use StatusCodeToReason;

    public function __construct(
        private int $status,
        array $headers,
        StreamInterface |string $body,
        string $protocolVersion,
        private string $reason
    ) {
        if (!$this->isStatusCodeValid($status))
            throw new \InvalidArgumentException('Invalid status code');

        parent::__construct($headers, $protocolVersion, $body);
    }

    public function getStatusCode()
    {
        return $this->status;
    }

    public function getReasonPhrase()
    {
        $reason = $this->reason;

        if (empty($reason)) {
            return $this->getReasonPhraseByCode($this->status);
        }

        return $this->reason;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        if (!$this->isStatusCodeValid($code))
            throw new \InvalidArgumentException('Invalid status code');

        if (empty($reasonPhrase)) {
            $reasonPhrase = $this->getReasonPhraseByCode($code);
        }

        $clone = clone $this;
        $clone->status = $code;
        $clone->reason = $reasonPhrase;

        return $clone;
    }
}
