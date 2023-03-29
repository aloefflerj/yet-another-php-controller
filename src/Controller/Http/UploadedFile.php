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

    public function moveTo($targetPath)
    {
        $dirPath = dirname($targetPath);
        if (!is_dir($dirPath)) {
            throw new \InvalidArgumentException("The given path '{$targetPath}' is not a valid path. Dir '$dirPath' does not exists");
        }

        if (!is_writable($dirPath)) {
            throw new \RuntimeException("The dir path '{$targetPath}' is not writable.");
        }

        $sourcePath = $this->source->getMetadata('uri');
        if (!rename($sourcePath, $targetPath)) {
            $errorMsg = "There was an error when trying to move the file.\n";
            $errorMsg .= "Source path: {$sourcePath}\n";
            $errorMsg .= "Target path: {$targetPath}\n";
            throw new \RuntimeException($errorMsg);
        }
    }
}
