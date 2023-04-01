<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    const FILE_PATH = __DIR__ . '/StreamDummyFile.txt';
    const DUMMY_TEXT = 'This is just a dummy file. Nothing to see here. Sorry :/';
    static $dummyMetadata = [
        "timed_out" => false,
        "blocked" => true,
        "eof" => false,
        "wrapper_type" => "plainfile",
        "stream_type" => "STDIO",
        "mode" => "r+",
        "unread_bytes" => 0,
        "seekable" => true,
        "uri" => __DIR__ . "/StreamDummyFile.txt"
    ];

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

        $this->expectException(\TypeError::class);
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
        $stream->close();
    }

    public function testGetSize(): void
    {
        $resource = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($resource);
        $stream->write(self::DUMMY_TEXT);
        $this->assertEquals(56, $stream->getSize());

        $stream->detach();
        $this->assertNull($stream->getSize());
        $stream->close();
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
        $stream->close();
    }

    public function testEof(): void
    {
        $resource = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($resource);
        $stream->seek(10);
        $this->assertFalse($stream->eof());

        $stream->seek(0, SEEK_END);
        $this->assertTrue($stream->eof());
        $stream->close();
    }

    public function testSeek(): void
    {
        $resource = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($resource);
        $stream->seek(42);
        $this->assertEquals(42, $stream->tell());

        $stream->seek(8, SEEK_CUR);
        $this->assertEquals(50, $stream->tell());

        $stream->seek(0, SEEK_END);
        $this->assertEquals($stream->getSize(), $stream->tell());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/.*. Unable to seek to stream at position\s+\-?\d+\.$/');
        $stream->seek(-20000);

        $stream->close();

        $resource = fopen('https://google.com', 'rb');
        $stream = new Stream($resource);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Stream is not seakable');
        $stream->seek(42);
        $stream->close();
    }

    public function testRewind(): void
    {
        $resource = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($resource);
        $stream->rewind();

        $this->assertEquals(0, $stream->tell());

        $stream->close();

        $resource = fopen('https://google.com', 'rb');
        $stream = new Stream($resource);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Stream is not seakable');
        $stream->rewind();
        $stream->close();
    }

    public function testWrite(): void
    {
        $dummyText = 'Another silly dummy text, only. :P';
        $stream = new Stream(fopen('php://temp', 'r+'));
        $bytesWritten = $stream->write($dummyText);
        $this->assertEquals($stream->getSize(), $bytesWritten);

        $this->assertEquals($dummyText, $stream->getContents());

        $stream->close();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Unable to write \'.*\' to stream$/');
        $stream = new Stream(fopen('php://temp', 'r'));
        $stream->write('I\'m not going to be written down. :(');
        $stream->close();
    }

    public function testRead(): void
    {
        $resource = fopen(self::FILE_PATH, 'r');
        $stream = new Stream($resource);
        $content = $stream->read(26);
        $this->assertEquals('This is just a dummy file.', $content);

        $stream->close();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to read from stream');
        $resource = fopen(self::FILE_PATH, 'w');
        $stream = new Stream($resource);
        $stream->read(1);
        $stream->close();
    }

    public function testGetContents(): void
    {
        $resource = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($resource);
        $stream->write(self::DUMMY_TEXT);
        $this->assertEquals(self::DUMMY_TEXT, $stream->getContents());
        $stream->close();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to read from file');
        $resource = fopen(self::FILE_PATH, 'w');
        $stream = new Stream($resource);
        $stream->write(self::DUMMY_TEXT);
        $this->assertEquals(self::DUMMY_TEXT, $stream->getContents());
        $stream->close();
    }

    public function testGetMetadata(): void
    {
        $resource = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($resource);
        $stream->write(self::DUMMY_TEXT);
        $this->assertEquals(self::$dummyMetadata, $stream->getMetadata());
        $stream->close();
    }

    public function testToString(): void
    {
        $resource = fopen(self::FILE_PATH, 'r+');
        $stream = new Stream($resource);
        $stream->write(self::DUMMY_TEXT);

        $this->assertEquals(self::DUMMY_TEXT, $stream);
    }
}
