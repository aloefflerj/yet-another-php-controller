<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testMethod(): void
    {
        $request = new Request('GET');
        $this->assertEquals('GET', $request->getMethod());

        $request = new Request('get');
        $this->assertEquals('GET', $request->getMethod());
        
        $request = $request->withMethod('POST');
        $this->assertEquals('POST', $request->getMethod());

        $request = $request->withMethod('put');
        $this->assertEquals('PUT', $request->getMethod());
    }
}