<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    function testToString(): void
    {
        $uriString = 'http://teste.com/';
        $uriPath = strval(new Uri($uriString));
        $this->assertSame($uriString, $uriPath);
    }
}
