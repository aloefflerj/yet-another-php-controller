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
    public function testServerParams()
    {
        $serverRequest = new ServerRequest('GET');
        $this->assertEquals($_SERVER, $serverRequest->getServerParams());
    }
}