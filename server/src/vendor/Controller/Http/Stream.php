<?php

namespace Aloefflerj\FedTheDog\Controller\Http;

use Aloefflerj\FedTheDog\Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{

    public $stream;
    private bool $writable;
    private bool $readable;
    private ?int $size;

    public function __construct($resource)
    {
        $this->stream = $resource;

        $this->writable = false;
        $this->readable = false;

        $meta = $this->getMetadata();

        // The mode parameter specifies the type of access you require to 
        // the stream. @see https://www.php.net/manual/en/function.fopen.php
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
    }

    public function __toString()
    {
        echo $this->read($this->getSize());
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

    public function getSize()
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

    public function tell()
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
        return $this->stream ? feof($this->stream) : true;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->seekable) {
            throw new \RuntimeException(
                'Stream is not seakable'
            );
        }

        $offset = (int) $offset;
        $whence = (int) $whence;

        $message = [
            SEEK_CUR => 'Set position to current location plus offset.',
            SEEK_END => 'Set position to end-of-stream plus offset.',
            SEEK_SET => 'Set position equal to offset bytes.',
        ];

        $errorMsg = $message[$whence] ?? 'Unknown error.';

        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new \RuntimeException(
                sprintf(
                    '%s. Unable to seek to stream at position %s',
                    $errorMsg,
                    $offset
                )
            );
        }
    }

    public function rewind()
    {
        $this->seek(0);
    }

    public function write($string)
    {
        $size = 0;

        if ($this->isStream() && $this->isWritable()) {
            $size = fwrite($this->stream, $string);
        }

        if ($size === false) {
            throw new \RuntimeException(
                'Unable to write %s to stream',
                $string
            );
        }

        $this->size = null;

        return $size;
    }

    public function read($length)
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

    public function getContents()
    {   
        if(!$this->isReadable()) {
            throw new \RuntimeException(
                'Unable to read from file'
            );
        }

        $string = stream_get_contents($this->stream);

        if($string === false) {
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
