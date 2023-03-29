<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use Aloefflerj\YetAnotherController\Controller\Http\UploadedFile;
use PHPUnit\Framework\TestCase;

class UploadedFileTest extends TestCase
{
    const FILE_NAME = 'StreamDummyFile.txt';
    const FILE_PATH = __DIR__ . '/StreamDummyFile.txt';
    const EMPTY_DIR = __DIR__ . '/emptyDir';

    public function testStream(): void
    {
        $dummyFile = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($dummyFile);
        $uploadedFile = new UploadedFile($stream);
        $this->assertEquals($stream, $uploadedFile->getStream());
    }

    public function testMoveTo(): void
    {
        $dummyFile = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($dummyFile);
        $uploadedFile = new UploadedFile($stream);
        $uploadedFile->moveTo(self::EMPTY_DIR . '/' . self::FILE_NAME);
        $this->assertTrue(file_exists(self::EMPTY_DIR . '/' . self::FILE_NAME));
        $this->assertFalse(file_exists(self::FILE_PATH));

        $this->returnFileToOriginalPath();
    }
    
    private function returnFileToOriginalPath(): void
    {
        rename(self::EMPTY_DIR . '/' . self::FILE_NAME, __DIR__ . '/' . self::FILE_NAME);
    }
}
