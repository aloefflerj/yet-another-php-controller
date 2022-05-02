<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Aloefflerj\YetAnotherController\Controller\PSR\StreamInterface;

class Stream implements StreamInterface
{
    public $stream;
    private bool $writable;
    private bool $readable;
    private ?int $size;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException('Argument must be a valid resource type');
        }

        $this->stream = $resource;

        $this->writable = false;
        $this->readable = false;

        $meta = $this->getMetadata();

        if (strpos($meta['mode'], '+') !== false) {
            $this->readable = true;
            $this->writable = true;
        }

        if (preg_match('/^[waxc][t|b]{0,1}$/', $meta['mode'], $matches, PREG_OFFSET_CAPTURE)) {
            $this->writable = true;
        }

        if (strpos($meta['mode'], 'r') !== false) {
            $this->readable = true;
        }

        $this->seekable = $meta['seekable'];
        $this->size = null;
    }

    public function __toString(): string
    {
        return $this->getContents();
    }

    public function isWritable()
    {
        return $this->writable;
    }

    public function isReadable()
    {
        return $this->readable;
    }

    public function isSeekable()
    {
        return $this->seekable;
    }

    public function getMetadata($key = null)
    {
        $meta = stream_get_meta_data($this->stream) ?? null;

        if (!$meta) {
            return null;
        }

        if ($key) {
            if (array_key_exists($key, $meta)) {
                return $meta[$key];
            }
            return null;
        }

        return $meta;
    }

    public function close(): void
    {
        if ($this->isStream()) {
            fclose($this->stream);
        }

        $this->detach();
    }

    /**
     * @return resource|null
     */
    public function detach()
    {
        if (!$this->isStream()) {
            return null;
        }

        $resource = $this->stream;

        $this->readable = false;
        $this->writable = false;
        $this->seekable = false;
        $this->size = null;
        $this->meta = [];

        unset($this->stream);

        return $resource;
    }

    public function getSize(): ?int
    {
        if (!$this->isStream()) {
            return null;
        }

        if ($this->size === null) {
            $stats = fstat($this->stream);
            $this->size = $stats['size'] ?? null;
        }

        return $this->size;
    }

    /**
     * @throws \RuntimeException
     */
    public function tell(): int
    {
        $pointer = false;

        if ($this->stream) {
            $pointer = ftell($this->stream);
        }

        if ($pointer === false) {
            throw new \RuntimeException(
                'Unable to get position of file pointer'
            );
        }

        return $pointer;
    }

    public function eof(): bool
    {
        return $this->stream ? $this->tell() === $this->getSize()  : true;
    }

    /**
     * @throws \RuntimeException
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        if (!$this->seekable) {
            throw new \RuntimeException(
                'Stream is not seakable'
            );
        }

        $offset = (int) $offset;
        $whence = (int) $whence;

        $message = [
            SEEK_SET => 'Set position equal to offset bytes.',
            SEEK_CUR => 'Set position to current location plus offset.',
            SEEK_END => 'Set position to end-of-stream plus offset.',
        ];

        $errorMsg = $message[$whence] ?? 'Unknown error.';

        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new \RuntimeException(
                "{$errorMsg}. Unable to seek to stream at position {$offset}."
            );
        }
    }

    /**
     * @throws \RuntimeException
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * @throws \RuntimeException
     */
    public function write($string): int
    {
        $size = 0;

        if ($this->isStream()) {
            $size = fwrite($this->stream, $string);
        }

        if ($size === false) {
            throw new \RuntimeException(
                "Unable to write '{$string}' to stream",
            );
        }

        $this->size = null;

        return $size;
    }

    public function read($length): string
    {
        $string = false;

        if ($this->isStream() && $this->isReadable()) {
            $string = fread($this->stream, $length);
        }

        if ($string === false) {
            throw new \RuntimeException(
                'Unable to read from stream'
            );
        }

        return $string;
    }

    public function getContents(): string
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException(
                'Unable to read from file'
            );
        }

        $string = stream_get_contents($this->stream, $this->getSize(), 0);

        if ($string === false) {
            throw new \RuntimeException(
                'Unable to read from stream'
            );
        }

        return $string;
    }

    protected function isStream(): bool
    {
        return (isset($this->stream) && is_resource($this->stream));
    }
}
