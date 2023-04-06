<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response extends Message #implements ResponseInterface
{
    public function __construct(
        private int $status,
        array $headers,
        StreamInterface |string $body,
        string $protocolVersion,
        private string $reason
    ) {
        parent::__construct($headers, $protocolVersion, $body);
    }
}
