<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use Aloefflerj\YetAnotherController\Controller\Http\UploadedFile;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class UploadedFileTest extends TestCase
{
    const FILE_NAME = 'StreamDummyFile.txt';
    const FILE_PATH = __DIR__ . '/StreamDummyFile.txt';
    const EMPTY_DIR = __DIR__ . '/emptyDir';

    private mixed $dummyFile;
    private StreamInterface $stream;
    private UploadedFile $uploadedFile;

    protected function setUp(): void
    {
        $this->dummyFile = fopen(self::FILE_PATH, 'r+');
        $this->stream = new Stream($this->dummyFile);
        $this->uploadedFile = new UploadedFile($this->stream);
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

    private function returnFileToOriginalPath(): void
    {
        rename(self::EMPTY_DIR . '/' . self::FILE_NAME, __DIR__ . '/' . self::FILE_NAME);
    }
}
