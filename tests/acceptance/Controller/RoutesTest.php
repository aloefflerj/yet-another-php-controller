<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\BaseController;
use Aloefflerj\YetAnotherController\Controller\Http\Message;
use Aloefflerj\YetAnotherController\Controller\Http\Stream;
use Aloefflerj\YetAnotherController\Tests\Helpers\WebServerHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class RoutesTest extends TestCase
{
    private ?WebServerHelper $server = null;

    const MASCOTS_FIXTURE = [
        [
            "id" => 1,
            "language" => "php",
            "mascot" => "elephant"
        ],
        [
            "id" => 2,
            "language" => "go",
            "mascot" => "gopher"
        ]
    ];

    protected function setUp(): void
    {
        $this->server = new WebServerHelper('localhost:9000', '', 'RoutesTest', 'routeProvider');
        $this->server->startWebServer();
    }

    public function testHomeRoute(): void
    {
        $client = $this->server->makeClient();
        $response = $client->testRequest('GET', '');
        $this->assertEquals('home', strval($response->getBody()));
    }

    public function testJsonReturnRoute(): void
    {
        $client = $this->server->makeClient();
        $response = $client->testRequest('GET', 'mascots');
        $this->assertEquals(json_encode(self::MASCOTS_FIXTURE), strval($response->getBody()));
    }

    public function routeProvider(): \closure
    {
        return function () {
            $app = new BaseController('/RoutesTest/routeProvider');

            $app->get('/', function ($req, $res, $headerParams, $functionParams) {
                echo 'home';
            });

            $app->get('/mascots', function ($req, $res, $headerParams, $functionParams) {
                echo json_encode(self::MASCOTS_FIXTURE);
            });

            $app->dispatch();
        };
    }
}
