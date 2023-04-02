<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use Aloefflerj\YetAnotherController\Controller\Http\UploadedFile;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFileTest extends TestCase
{
    const FILE_NAME = 'StreamDummyFile.txt';
    const FILE_PATH = __DIR__ . '/StreamDummyFile.txt';
    const EMPTY_DIR = __DIR__ . '/emptyDir';
    const FILE_TYPE = 'text/plain';

    private mixed $dummyFile;
    private StreamInterface $stream;
    private UploadedFileInterface $uploadedFile;

    protected function setUp(): void
    {
        $this->dummyFile = fopen(self::FILE_PATH, 'r+');
        $this->stream = new Stream($this->dummyFile);
        $this->uploadedFile = new UploadedFile(
            $this->stream,
            self::FILE_NAME,
            self::FILE_TYPE,
            56
        );
    }

    public function testStream(): void
    {
        $stream = $this->stream;
        $uploadedFile = $this->uploadedFile;

        $this->assertEquals($stream, $uploadedFile->getStream());
    }

    public function testMoveTo(): void
    {
        $uploadedFile = $this->uploadedFile;

        $uploadedFile->moveTo(self::EMPTY_DIR . '/' . self::FILE_NAME);
        $this->assertTrue(file_exists(self::EMPTY_DIR . '/' . self::FILE_NAME));
        $this->assertFalse(file_exists(self::FILE_PATH));

        $this->returnFileToOriginalPath();
    }

    public function testGetSize(): void
    {
        $uploadedFile = $this->uploadedFile;

        $this->assertEquals(56, $uploadedFile->getSize());
    }

    public function testGetError(): void
    {
        $uploadedFile = $this->uploadedFile;

        $this->assertEquals(0, $uploadedFile->getError());
    }

    public function testGetClientFilename(): void
    {
        $uploadedFile = $this->uploadedFile;

        $this->assertEquals(self::FILE_NAME, $uploadedFile->getClientFilename());
    }

    public function testGetClientMediaType(): void
    {
        $uploadedFile = $this->uploadedFile;

        $this->assertEquals(self::FILE_TYPE, $uploadedFile->getClientMediaType());
    }

    private function returnFileToOriginalPath(): void
    {
        rename(self::EMPTY_DIR . '/' . self::FILE_NAME, __DIR__ . '/' . self::FILE_NAME);
    }
}
