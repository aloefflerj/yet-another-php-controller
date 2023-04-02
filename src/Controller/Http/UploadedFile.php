<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
    public function __construct(
        private StreamInterface $source,
        private ?string $name = null,
        private ?string $type = null,
        private ?int $size = null,
        private int $error = UPLOAD_ERR_OK
    ) {
        $this->assertMaxFileSize();
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

    public function getSize()
    {
        return $this->source->getSize();
    }

    public function getError()
    {
        return $this->error;
    }

    public function getClientFilename()
    {
        return $this->name;
    }

    public function getClientMediaType()
    {
        return $this->type;
    }

    private function assertMaxFileSize(): void
    {
        $fileSizeInBytes = $this->getSize();

        $maxFileSize = ini_get('upload_max_filesize');
        $maxFileSizeInBytes = $this->convertSizeToBytes($maxFileSize);

        if ($fileSizeInBytes > $maxFileSizeInBytes)
            $this->error = UPLOAD_ERR_INI_SIZE;
    }

    private function convertSizeToBytes(string $size)
    {
        $suffix = strtoupper(substr($size, -1));
        if (!in_array($suffix, ['P', 'T', 'G', 'M', 'K'])) {
            return (int)$size;
        }
        $iValue = substr($size, 0, -1);
        switch ($suffix) {
            case 'P':
                $iValue *= 1024;
                // Fallthrough intended
            case 'T':
                $iValue *= 1024;
                // Fallthrough intended
            case 'G':
                $iValue *= 1024;
                // Fallthrough intended
            case 'M':
                $iValue *= 1024;
                // Fallthrough intended
            case 'K':
                $iValue *= 1024;
                break;
        }
        return (int)$iValue;
    }
}
