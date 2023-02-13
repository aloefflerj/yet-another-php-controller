<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use Aloefflerj\YetAnotherController\Controller\Http\UploadedFile;
use PHPUnit\Framework\TestCase;

class UploadedFileTest extends TestCase
{
    const FILE_PATH = __DIR__ . '/StreamDummyFile.txt';
    
    public function testStream(): void
    {
        $dummyFile = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($dummyFile);
        $uploadedFile = new UploadedFile($stream);
        $this->assertEquals($stream, $uploadedFile->getStream());
    }
}