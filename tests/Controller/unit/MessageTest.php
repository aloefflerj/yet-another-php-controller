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


}