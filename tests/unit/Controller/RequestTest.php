<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Method;
use Aloefflerj\YetAnotherController\Controller\Http\Request;
use Aloefflerj\YetAnotherController\Controller\Http\Uri;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testMethod(): void
    {
        $request = new Request('GET');
        $this->assertEquals('GET', $request->getMethod());

        $request = new Request('get');
        $this->assertEquals('GET', $request->getMethod());

        $request = new Request(Method::GET);
        $this->assertEquals('GET', $request->getMethod());
        
        $request = $request->withMethod('POST');
        $this->assertEquals('POST', $request->getMethod());

        $request = $request->withMethod('put');
        $this->assertEquals('PUT', $request->getMethod());

        $request = $request->withMethod(Method::PUT);
        $this->assertEquals('PUT', $request->getMethod());
    }

    public function testUri(): void
    {
        $request = new Request('GET');
        $this->assertInstanceOf(Uri::class, $request->getUri());
        $this->assertEquals('', strval($request->getUri()));
        
        $request = new Request('GET', 'http://test.com');
        $this->assertInstanceOf(Uri::class, $request->getUri());
        $this->assertEquals('http://test.com/', strval($request->getUri()));
        
        $request = $request->withUri(new Uri('http://newtest.com'));
        $this->assertInstanceOf(Uri::class, $request->getUri());
        $this->assertEquals('http://newtest.com/', strval($request->getUri()));

        $request = new Request('GET', 'http://must.vanish.away');
        $request = $request->withUri(new Uri('http://must.remain.here'));
        $this->assertInstanceOf(Uri::class, $request->getUri());
        $this->assertEquals('must.remain.here', $request->getUri()->getHost());

        $request = new Request('GET', 'http://must.remain.here');
        $request = $request->withUri(new Uri('http://must.vanish.away'), true);
        $this->assertInstanceOf(Uri::class, $request->getUri());
        $this->assertEquals('must.remain.here', $request->getUri()->getHost());

        $request = new Request('GET');
        $request = $request->withUri(new Uri('http://must.remain.here'), true);
        $this->assertInstanceOf(Uri::class, $request->getUri());
        $this->assertEquals('must.remain.here', $request->getUri()->getHost());
        
        $uri = new Uri();
        $request = new Request('GET', $uri);
        $this->assertInstanceOf(Uri::class, $request->getUri());
        $this->assertEquals('', strval($request->getUri()));

        $uri = new Uri('http://yetanothertest.com');
        $request = new Request('GET', $uri);
        $this->assertInstanceOf(Uri::class, $request->getUri());
        $this->assertEquals('http://yetanothertest.com/', strval($request->getUri()));
    }
}