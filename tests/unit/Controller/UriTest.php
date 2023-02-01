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
            'path' => '/',
            'query' => '',
            'fragment' => ''
        ],
        1 => [
            'uri' => 'http://aloefflerj:12345@github.com/repositories?p=113&foo=bar#yes-i-do',
            'scheme' => 'http',
            'authority' => 'aloefflerj:12345@github.com',
            'userInfo' => 'aloefflerj:12345',
            'host' => 'github.com',
            'port' => '',
            'path' => '/repositories',
            'query' => 'p=113&foo=bar',
            'fragment' => 'yes-i-do'
        ],
        2 => [
            'uri' => 'http://localhost:80/users/1',
            'scheme' => 'http',
            'authority' => 'localhost:80',
            'userInfo' => '',
            'host' => 'localhost',
            'port' => 80,
            'path' => '/users/1',
            'query' => '',
            'fragment' => ''
        ],
        3 => [
            'uri' => 'ftp://user@host/foo/bar.txt',
            'scheme' => 'ftp',
            'authority' => 'user@host',
            'userInfo' => 'user',
            'host' => 'host',
            'port' => '',
            'path' => '/foo/bar.txt',
            'query' => '',
            'fragment' => ''
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

    public function testGetQuery(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);
            $query = $uri->getQuery();
            $this->assertEquals($example['query'], $query);
        }
    }

    public function testGetFragment(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);
            $fragment = $uri->getfragment();
            $this->assertEquals($example['fragment'], $fragment);
        }
    }

    public function testWithScheme(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);
            $uri = $uri->withScheme('file');
            $scheme = $uri->getScheme();
            $this->assertEquals('file', $scheme);

            $this->expectException(\InvalidArgumentException::class);
            $uri = $uri->withScheme('-wrongscheme');
            $scheme = $uri->getScheme();

            $this->expectException(\InvalidArgumentException::class);
            $uri = $uri->withScheme('WRONGSCHEME');
            $scheme = $uri->getScheme();
        }
    }

    public function testWithUserInfo(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);
            $uri = $uri->withUserInfo('john', '12345');
            $userInfo = $uri->getUserInfo();
            $this->assertEquals('john:12345', $userInfo);
        }
    }

    public function testWithHost(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);
            $uri = $uri->withHost('test.io');
            $host = $uri->getHost();
            $this->assertEquals('test.io', $host);

            $uri = $uri->withHost('test.com.br');
            $host = $uri->getHost();
            $this->assertEquals('test.com.br', $host);
            
            $this->expectException(\InvalidArgumentException::class);
            $uri = $uri->withHost('!hello.com');
            $host = $uri->getHost();

            $this->expectException(\InvalidArgumentException::class);
            $uri = $uri->withHost('hello@world.com.br');
            $host = $uri->getHost();
        }
    }

    public function testWithPort(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);

            $uri = $uri->withPort(3000);
            $port = $uri->getPort();
            $this->assertEquals(3000, $port);

            $uri = $uri->withPort(80);
            $port = $uri->getPort();
            $this->assertEquals(80, $port);

            $uri = $uri->withPort(null);
            $port = $uri->getPort();
            $this->assertEquals(null, $port);
            
            $this->expectException(\InvalidArgumentException::class);
            $uri = $uri->withPort(424242);
            $port = $uri->getPort();
        }
    }

    public function testWithPath(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);

            $uri = $uri->withPath('/new-path');
            $path = $uri->getPath();
            $this->assertEquals('/new-path', $path);

            $this->expectException(\InvalidArgumentException::class);
            $uri = $uri->withPath('::not a valid path::');
            $path = $uri->getPath();
        }
    }

    public function testWithQuery(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);

            $uri = $uri->withQuery('p=120&foo=baz');
            $query = $uri->getQuery();
            $this->assertEquals('p=120&foo=baz', $query);

            $this->expectException(\InvalidArgumentException::class);
            $uri = $uri->withPath('==sdf=');
            $query = $uri->getQuery();

            $this->expectException(\InvalidArgumentException::class);
            $uri = $uri->withPath('p=120=');
            $query = $uri->getQuery();

            $this->expectException(\InvalidArgumentException::class);
            $uri = $uri->withPath('p=120&foo=baz=q');
            $query = $uri->getQuery();
        }
    }

    public function testWithFragment(): void
    {
        foreach (self::EXAMPLES as $example) {
            $uri = new Uri($example['uri']);

            $uri = $uri->withFragment('i-drank-too-much-coffee-help');
            $fragment = $uri->getFragment();
            $this->assertEquals('i-drank-too-much-coffee-help', $fragment);
        }
    }
}
