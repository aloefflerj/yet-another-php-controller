<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Method;
use Aloefflerj\YetAnotherController\Controller\Http\Request;
use Aloefflerj\YetAnotherController\Controller\Http\ServerRequest;
use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use Aloefflerj\YetAnotherController\Controller\Http\Uri;
use PHPUnit\Framework\TestCase;

class ServerRequestTest extends TestCase
{
    public function testServerParams(): void
    {
        $serverRequest = new ServerRequest('GET');
        $this->assertEquals($_SERVER, $serverRequest->getServerParams());
    }

    public function testCookieParams(): void
    {
        $_COOKIE['planet'] = 'mars';
        $serverRequest = new ServerRequest('GET');
        $this->assertEquals($_COOKIE, $serverRequest->getCookieParams());

        $_COOKIE['planet'] = 'mars';
        $serverRequest = new ServerRequest('GET');
        $serverRequest = $serverRequest->withCookieParams(['planet' => 'venus']);
        $this->assertEquals(['planet' => 'venus'], $serverRequest->getCookieParams());
    }

    public function testQueryParams()
    {
        $serverRequest = new ServerRequest('GET', new Uri('http://universe.com?star=sun&galaxy=milky-way'));
        $this->assertEquals([
            'star' => 'sun',
            'galaxy' => 'milky-way'
        ], $serverRequest->getQueryParams());

        $serverRequest = $serverRequest->withQueryParams([
            'star' => 'alpheratz',
            'galaxy' => 'andromeda'
        ]);
        $this->assertEquals([
            'star' => 'alpheratz',
            'galaxy' => 'andromeda'
        ], $serverRequest->getQueryParams());
    }
}
