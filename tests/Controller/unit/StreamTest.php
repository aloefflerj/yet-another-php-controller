<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    const FILE_PATH = __DIR__ . '/StreamDummyFile.txt';
    const DUMMY_TEXT = 'This is just a dummy file. Nothing to see here. Sorry :/';

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

    public function testClose(): void
    {
        $resource = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($resource);
        $stream->close();

        $this->expectWarning();
        $stream->getContents();
    }

    public function testDetach(): void
    {
        $resource = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($resource);
        $legacy = $stream->detach();
        $this->assertIsResource($legacy);

        $legacy = $stream->detach();
        $this->assertNull($legacy);
    }

    public function testGetSize(): void
    {
        $resource = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($resource);
        $stream->write(self::DUMMY_TEXT);
        $this->assertEquals(56, $stream->getSize());

        $stream->detach();
        $this->assertNull($stream->getSize());
    }

    public function testTell(): void
    {
        $resource = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($resource);
        $stream->write(self::DUMMY_TEXT);
        $stream->seek(42);
        $this->assertEquals(42, $stream->tell());

        $this->expectException(\RuntimeException::class);
        $stream->seek(-1);
        $stream->tell();
    }
}
