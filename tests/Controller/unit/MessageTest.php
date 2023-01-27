<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Message;
use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testProtocolVersion(): void
    {
        $message = new Message();
        $this->assertEquals('1.0', $message->getProtocolVersion());

        $message = $message->withProtocolVersion('1.1');
        $this->assertEquals('1.1', $message->getProtocolVersion());
    }

    public function testHeaders(): void
    {
        $message = new Message();
        $this->assertEquals([], $message->getHeaders());
        $this->assertEquals(false, $message->hasHeader('content-type'));
        $this->assertEquals([], $message->getHeader('content-type'));
        $this->assertEquals('', $message->getHeaderLine('content-type'));

        $message = $message->withHeader('content-type', 'application/json');
        $message = $message->withHeader('user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');

        $settedHeader = [
            'content-type' => 'application/json',
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
        ];

        $this->assertEquals($settedHeader, $message->getHeaders());
    }
}
