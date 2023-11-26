<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Http\Uri;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    const DUMMY_URI = 'http://test.com';

    #[Test]
    #[DataProvider('providesUris')]
    public function uri_is_being_converted_to_string_as_required_in_psr(
        string $uri
    ): void {
        $uriFullPath = strval(new Uri($uri));
        $this->assertEquals($uri, $uriFullPath);
    }

    #[Test]
    #[DataProvider('providesSchemes')]
    public function scheme_is_being_retrieved_as_described_in_psr(
        string $uri,
        string $scheme
    ): void {
        $uri = new Uri($uri);
        $this->assertEquals($scheme, $uri->getScheme());
    }

    #[Test]
    #[DataProvider('providesAuthorities')]
    public function authority_is_being_retrieved_as_described_in_psr(
        string $uri,
        string $authority
    ): void {
        $uri = new Uri($uri);
        $this->assertEquals($authority, $uri->getAuthority());
    }

    #[Test]
    #[DataProvider('providesUserInfos')]
    public function user_info_is_being_retrieved_as_described_in_psr(
        string $uri,
        string $userInfo
    ): void {
        $uri = new Uri($uri);
        $this->assertEquals($userInfo, $uri->getUserInfo());
    }

    #[Test]
    #[DataProvider('providesHosts')]
    public function host_is_being_retrieved_as_described_in_psr(
        string $uri,
        string $host
    ): void {
        $uri = new Uri($uri);
        $this->assertEquals($host, $uri->getHost());
    }

    #[Test]
    #[DataProvider('providesPorts')]
    public function port_is_being_retrieved_as_described_in_psr(
        string $uri,
        ?int $port
    ): void {
        $uri = new Uri($uri);
        $this->assertEquals($port, $uri->getPort());
    }

    #[Test]
    #[DataProvider('providesPaths')]
    public function path_is_being_retrieved_as_described_in_psr(
        string $uri,
        string $path
    ): void {
        $uri = new Uri($uri);
        $this->assertEquals($path, $uri->getPath());
    }

    #[Test]
    #[DataProvider('providesQueries')]
    public function query_is_being_retrieved_as_described_in_psr(
        string $uri,
        string $query
    ): void {
        $uri = new Uri($uri);
        $this->assertEquals($query, $uri->getQuery());
    }

    #[Test]
    #[DataProvider('providesFragments')]
    public function fragment_is_being_retrieved_as_described_in_psr(
        string $uri,
        string $fragment
    ): void {
        $uri = new Uri($uri);
        $this->assertEquals($fragment, $uri->getFragment());
    }

    #[Test]
    #[DataProvider('providesSchemes')]
    #[Depends('scheme_is_being_retrieved_as_described_in_psr')]
    public function scheme_is_being_set_as_described_in_psr(
        string $uri,
        string $scheme
    ): void {
        $uri = new Uri($uri);
        $uri = $uri->withScheme('file');
        $scheme = $uri->getScheme();
        $this->assertEquals('file', $scheme);
    }

    #[Test]
    public function exception_is_being_thrown_when_invalid_scheme_is_set(): void
    {
        $uri = new Uri(self::DUMMY_URI);

        $this->expectException(\InvalidArgumentException::class);
        $uri = $uri->withScheme('-invalidscheme');

        $this->expectException(\InvalidArgumentException::class);
        $uri = $uri->withScheme('INVALIDSCHEME');
    }

    #[Test]
    #[Depends('user_info_is_being_retrieved_as_described_in_psr')]
    public function user_info_is_being_set_as_described_in_psr(): void
    {
        $uri = new Uri(self::DUMMY_URI);
        $uri = $uri->withUserInfo('john', '12345');
        $this->assertEquals('john:12345', $uri->getUserInfo());
    }

    #[Test]
    #[DataProvider('providesHosts')]
    #[Depends('host_is_being_retrieved_as_described_in_psr')]
    public function host_is_being_set_as_described_in_psr(
        $uri,
        $host
    ): void {
        $uri = new Uri(self::DUMMY_URI);

        // TODO: change this work around
        if (empty($host)) {
            $host = 'test.com';
        }

        $uri = $uri->withHost($host);
        $this->assertEquals($host, $uri->getHost());
    }

    #[Test]
    #[Depends('host_is_being_retrieved_as_described_in_psr')]
    public function exception_is_being_thrown_when_invalid_host_is_set(): void
    {
        $uri = new Uri(self::DUMMY_URI);

        $this->expectException(\InvalidArgumentException::class);
        $uri = $uri->withHost('!hello.com');

        $this->expectException(\InvalidArgumentException::class);
        $uri = $uri->withHost('hello@world.com.br');
    }

    #[Test]
    #[Depends('port_is_being_retrieved_as_described_in_psr')]
    public function port_is_being_set_as_described_in_psr(): void
    {
        $uri = new Uri(self::DUMMY_URI);

        $uri = $uri->withPort(3000);
        $port = $uri->getPort();
        $this->assertEquals(3000, $port);

        $uri = $uri->withPort(80);
        $port = $uri->getPort();
        $this->assertEquals(80, $port);

        $uri = $uri->withPort(null);
        $port = $uri->getPort();
        $this->assertEquals(null, $port);
    }

    #[Test]
    #[Depends('port_is_being_retrieved_as_described_in_psr')]
    public function exception_is_being_thrown_when_invalid_port_is_set(): void
    {
        $uri = new Uri(self::DUMMY_URI);

        $this->expectException(\InvalidArgumentException::class);
        $uri = $uri->withPort(424242);

        $this->expectException(\InvalidArgumentException::class);
        $uri = $uri->withPort('teste');
    }

    #[Test]
    #[DataProvider('providesPaths')]
    #[Depends('path_is_being_retrieved_as_described_in_psr')]
    public function path_is_being_set_as_described_in_psr(
        string $uri,
        string $path
    ): void {
        $uri = new Uri($uri);

        // TODO: change this work around
        if (empty($path)) {
            $path = '/';
        }

        $uri = $uri->withPath($path);
        $this->assertEquals($path, $uri->getPath());
    }

    #[Test]
    #[Depends('path_is_being_retrieved_as_described_in_psr')]
    public function exception_is_being_thrown_when_invalid_path_is_set(): void
    {
        $uri = new Uri(self::DUMMY_URI);

        $this->expectException(\InvalidArgumentException::class);
        $uri = $uri->withPath('::not a valid path::');
    }

    #[Test]
    #[Depends('query_is_being_retrieved_as_described_in_psr')]
    public function query_is_being_set_as_described_in_psr(): void
    {
        $uri = new Uri(self::DUMMY_URI);

        $uri = $uri->withQuery('p=120&foo=baz');
        $query = $uri->getQuery();
        $this->assertEquals('p=120&foo=baz', $query);
    }

    #[Test]
    #[Depends('query_is_being_retrieved_as_described_in_psr')]
    public function exception_is_being_thrown_when_invalid_query_is_set(): void
    {
        $uri = new Uri(self::DUMMY_URI);

        $this->expectException(\InvalidArgumentException::class);
        $uri = $uri->withQuery('==sdf=');

        $this->expectException(\InvalidArgumentException::class);
        $uri = $uri->withQuery('p=120=');

        $this->expectException(\InvalidArgumentException::class);
        $uri = $uri->withQuery('p=120&foo=baz=q');
    }

    #[Test]
    #[Depends('fragment_is_being_retrieved_as_described_in_psr')]
    public function fragment_is_being_set_as_described_in_psr(): void
    {
        $uri = new Uri(self::DUMMY_URI);

        $uri = $uri->withFragment('i-drank-too-much-coffee-help');
        $fragment = $uri->getFragment();
        $this->assertEquals('i-drank-too-much-coffee-help', $fragment);
    }

    public static function providesUris(): array
    {
        return [
            'simple' => [
                'uri' => 'http://test.com/',
            ],
            'complex' => [
                'uri' => 'https://aloefflerj:12345@github.com/repositories?p=113&foo=bar#yes-i-do',
            ],
            'usual scenario' => [
                'uri' => 'http://localhost:80/users/1',
            ],
            'unusual scenario' => [
                'uri' => 'ftp://user@host/foo/bar.txt',
            ],
            'empty' => [
                'uri' => '',
            ]
        ];
    }

    public static function providesSchemes(): array
    {
        return [
            'simple' => [
                'uri' => 'http://test.com/',
                'scheme' => 'http',
            ],
            'complex' => [
                'uri' => 'microsoft.windows.camera.multipicker://aloefflerj:12345@github.com:8546/repositories?p=113&foo=bar#yes-i-do',
                'scheme' => 'microsoft.windows.camera.multipicker',
            ],
            'usual scenario' => [
                'uri' => 'https://localhost:80/users/1',
                'scheme' => 'https',
            ],
            'unusual scenario' => [
                'uri' => 'ftp://user@host/foo/bar.txt',
                'scheme' => 'ftp',
            ],
            'empty' => [
                'uri' => '',
                'scheme' => '',
            ]
        ];
    }

    public static function providesAuthorities(): array
    {
        return [
            'simple' => [
                'uri' => 'http://test.com/',
                'authority' => 'test.com',
            ],
            'complex' => [
                'uri' => 'microsoft.windows.camera.multipicker://aloefflerj:12345@github.com:8546/repositories?p=113&foo=bar#yes-i-do',
                'authority' => 'aloefflerj:12345@github.com:8546',
            ],
            'usual scenario' => [
                'uri' => 'https://localhost:80/users/1',
                'authority' => 'localhost:80',
            ],
            'unusual scenario' => [
                'uri' => 'ftp://user@host/foo/bar.txt',
                'authority' => 'user@host',
            ],
            'empty' => [
                'uri' => '',
                'authority' => '',
            ]
        ];
    }

    public static function providesUserInfos(): array
    {
        return [
            'simple' => [
                'uri' => 'http://test.com/',
                'userInfo' => '',
            ],
            'complex' => [
                'uri' => 'microsoft.windows.camera.multipicker://aloefflerj:12345@github.com:8546/repositories?p=113&foo=bar#yes-i-do',
                'userInfo' => 'aloefflerj:12345',
            ],
            'usual scenario' => [
                'uri' => 'https://localhost:80/users/1',
                'userInfo' => '',
            ],
            'unusual scenario' => [
                'uri' => 'ftp://user@host/foo/bar.txt',
                'userInfo' => 'user',
            ],
            'empty' => [
                'uri' => '',
                'userInfo' => '',
            ]
        ];
    }

    public static function providesHosts(): array
    {
        return [
            'simple' => [
                'uri' => 'http://test.com/',
                'host' => 'test.com',
            ],
            'complex' => [
                'uri' => 'microsoft.windows.camera.multipicker://aloefflerj:12345@github.com:8546/repositories?p=113&foo=bar#yes-i-do',
                'host' => 'github.com',
            ],
            'usual scenario' => [
                'uri' => 'https://localhost:80/users/1',
                'host' => 'localhost',
            ],
            'unusual scenario' => [
                'uri' => 'ftp://user@host/foo/bar.txt',
                'host' => 'host',
            ],
            'empty' => [
                'uri' => '',
                'host' => '',
            ]
        ];
    }

    public static function providesPorts(): array
    {
        return [
            'simple' => [
                'uri' => 'http://test.com/',
                'port' => null,
            ],
            'complex' => [
                'uri' => 'microsoft.windows.camera.multipicker://aloefflerj:12345@github.com:8546/repositories?p=113&foo=bar#yes-i-do',
                'port' => 8546,
            ],
            'usual scenario' => [
                'uri' => 'https://localhost:80/users/1',
                'port' => 80,
            ],
            'unusual scenario' => [
                'uri' => 'ftp://user@host/foo/bar.txt',
                'port' => null,
            ],
            'empty' => [
                'uri' => '',
                'port' => null,
            ]
        ];
    }

    public static function providesPaths(): array
    {
        return [
            'simple' => [
                'uri' => 'http://test.com/',
                'path' => '/',
            ],
            'complex' => [
                'uri' => 'microsoft.windows.camera.multipicker://aloefflerj:12345@github.com:8546/repositories?p=113&foo=bar#yes-i-do',
                'path' => '/repositories',
            ],
            'usual scenario' => [
                'uri' => 'https://localhost:80/users/1',
                'path' => '/users/1',
            ],
            'unusual scenario' => [
                'uri' => 'ftp://user@host/foo/bar.txt',
                'path' => '/foo/bar.txt',
            ],
            'empty' => [
                'uri' => '',
                'path' => '',
            ]
        ];
    }

    public static function providesQueries(): array
    {
        return [
            'simple' => [
                'uri' => 'http://test.com/',
                'query' => '',
            ],
            'complex' => [
                'uri' => 'microsoft.windows.camera.multipicker://aloefflerj:12345@github.com:8546/repositories?p=113&foo=bar#yes-i-do',
                'query' => 'p=113&foo=bar',
            ],
            'usual scenario' => [
                'uri' => 'https://localhost:80/users/1',
                'query' => '',
            ],
            'unusual scenario' => [
                'uri' => 'ftp://user@host/foo/bar.txt',
                'query' => '',
            ],
            'empty' => [
                'uri' => '',
                'query' => '',
            ]
        ];
    }

    public static function providesFragments(): array
    {
        return [
            'simple' => [
                'uri' => 'http://test.com/',
                'fragment' => ''
            ],
            'complex' => [
                'uri' => 'microsoft.windows.camera.multipicker://aloefflerj:12345@github.com:8546/repositories?p=113&foo=bar#yes-i-do',
                'fragment' => 'yes-i-do'
            ],
            'usual scenario' => [
                'uri' => 'https://localhost:80/users/1',
                'fragment' => ''
            ],
            'unusual scenario' => [
                'uri' => 'ftp://user@host/foo/bar.txt',
                'fragment' => ''
            ],
            'empty' => [
                'uri' => '',
                'fragment' => ''
            ]
        ];
    }
}
