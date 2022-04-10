<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    const EXAMPLES = [
        0 => [
            'uri' => 'https://test.com/',
            'scheme' => 'https',
            'authority' => 'test.com',
            'userInfo' => '',
            'host' => 'test.com',
            'port' => '',
            'path' => '/'
        ],
        1 => [
            'uri' => 'http://aloefflerj:12345@github.com/repositories',
            'scheme' => 'http',
            'authority' => 'aloefflerj:12345@github.com',
            'userInfo' => 'aloefflerj:12345',
            'host' => 'github.com',
            'port' => '',
            'path' => '/repositories'
        ],
        2 => [
            'uri' => 'http://localhost:80/users/1',
            'scheme' => 'http',
            'authority' => 'localhost:80',
            'userInfo' => '',
            'host' => 'localhost',
            'port' => 80,
            'path' => '/users/1'
        ],
        3 => [
            'uri' => 'ftp://user@host/foo/bar.txt',
            'scheme' => 'ftp',
            'authority' => 'user@host',
            'userInfo' => 'user',
            'host' => 'host',
            'port' => '',
            'path' => '/foo/bar.txt'
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

    public function testGetAuthority(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);
            $authority = $uri->getAuthority();
            $this->assertEquals($example['authority'], $authority);
        }
    }

    public function testGetUserInfo(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);
            $userInfo = $uri->getUserInfo();
            $this->assertEquals($example['userInfo'], $userInfo);
        }
    }

    public function testGetHost(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);
            $host = $uri->getHost();
            $this->assertEquals($example['host'], $host);
        }
    }

    public function testGetPort(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);
            $port = $uri->getPort();
            $this->assertEquals($example['port'], $port);
        }
    }

    public function testGetPath(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);
            $path = $uri->getPath();
            $this->assertEquals($example['path'], $path);
        }
    }
}
