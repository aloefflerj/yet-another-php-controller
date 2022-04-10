<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    const EXAMPLES = [
        0 => [
            'uri' => 'https://teste.com/',
            'scheme' => 'https'
        ],
        1 => [
            'uri' => 'http://aloefflerj:12345@github.com/repositories',
            'scheme' => 'http'
        ]
    ];

    public function testUriToString(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uriFullPath = strval(new Uri($example['uri']));
            $this->assertEquals($example['uri'], $uriFullPath);
        }
    }

    public function testGetScheme(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);
            $scheme = $uri->getScheme();
            $this->assertEquals($example['scheme'], $scheme);
        }
    }
}
