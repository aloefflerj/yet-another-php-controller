<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use PHPUnit\Framework\TestCase;

class StramTest extends TestCase
{
    const FILE_PATH = __DIR__ . '/StreamDummyFile.txt';

    public function testConstruct(): void
    {
        $resource = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($resource);

        $this->expectException(\InvalidArgumentException::class);
        $resource = self::FILE_PATH;
        $stream = new Stream($resource);
    }

    public function testIsWritable(): void
    {
        $resource = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($resource);
        $this->assertTrue($stream->isWritable());

        $resource = fopen(self::FILE_PATH, 'r');
        $stream = new Stream($resource);
        $this->assertFalse($stream->isWritable());
    }

    public function testIsReadable(): void
    {
        $resource = fopen(self::FILE_PATH, 'r');
        $stream = new Stream($resource);
        $this->assertTrue($stream->isReadable());

        $resource = fopen(self::FILE_PATH, 'w');
        $stream = new Stream($resource);
        $this->assertFalse($stream->isReadable());
    }
    
    public function testIsSeekable(): void 
    {
        $resource = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($resource);
        $this->assertTrue($stream->isSeekable());
    }
}
