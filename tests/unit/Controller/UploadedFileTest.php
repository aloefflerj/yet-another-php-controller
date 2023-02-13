<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Message;
use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use PHPUnit\Framework\TestCase;

class UploadedFileTest extends TestCase
{
    public function testStream(): void
    {
        $dummyFile = fopen('./StreamDummyFile.txt', 'r+');
        $stream = new Stream($dummyFile);
        $uploadedFile = new UploadedFile($stream);
        $this->assertEquals($stream, $uploadedFile->getStream());
    }
}