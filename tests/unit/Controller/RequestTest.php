<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Message;
use Aloefflerj\YetAnotherController\Controller\Http\Request;
use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testMethod(): void
    {
        $request = new Request('GET');
        $this->assertEquals('GET', $request->getMethod());
    }
}