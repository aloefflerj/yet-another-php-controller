<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFile #implements UploadedFileInterface
{
    public function __construct(
        private StreamInterface $source,
        private ?string $name = null,
        private ?string $type = null,
        private ?int $size = null,
        private int $error = 0
    ) {
        
    }

    public function getStream()
    {
        return $this->source;
    }
}