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
            'content-type' => ['application/json'],
            'user-agent' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64)']
        ];
        $this->assertEquals($settedHeader, $message->getHeaders());

        $message = $message->withHeader('content-type', 'text/html');
        $settedHeader['content-type'] = ['text/html'];
        $this->assertEquals($settedHeader, $message->getHeaders());
        
        $settedHeader['content-type'][] = 'application/json';
        $message = $message->withAddedHeader('content-type', 'application/json');
        $this->assertEquals($settedHeader, $message->getHeaders());
        
        $settedHeader['origin'] = ['127.0.0.1:80', '127.0.0.1:443'];
        $message = $message->withAddedHeader('origin', ['127.0.0.1:80', '127.0.0.1:443']);
        $this->assertEquals($settedHeader, $message->getHeaders());

        unset($settedHeader['origin']);
        $message = $message->withoutHeader('origin');
        $this->assertEquals($settedHeader, $message->getHeaders());
        $message = $message->withoutHeader('this-does-not-exists');
        $this->assertEquals($settedHeader, $message->getHeaders());
    }

    public function testBody(): void
    {
        $message = new Message();
        $stream = new Stream(fopen('php://temp', 'r+'));

        $michaelFamousSaying = 'You miss 100% of the shots you don\'t take';
        $stream->write($michaelFamousSaying);

        $message = $message->withBody($stream);
        $this->assertEquals($michaelFamousSaying, $message->getBody());
    }
}
