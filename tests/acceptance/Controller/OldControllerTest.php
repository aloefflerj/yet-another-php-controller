<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Tests\Helpers\TestClient;
use Aloefflerj\YetAnotherController\Tests\Helpers\TestControllerBuilder;
use Aloefflerj\YetAnotherController\Tests\Helpers\WebServerHelper;
use PHPUnit\Framework\TestCase;

class OldControllerTest extends TestCase
{
    private ?WebServerHelper $server = null;
    private TestClient $client;

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
        $this->server = new WebServerHelper('localhost:9000', '', 'OldControllerTest', 'routeProvider');
        $this->server->startWebServer();

        $this->client = $this->server->makeClient();
    }

    public function testHomeRoute(): void
    {
        $response = $this->client->testRequest('GET', '');
        $this->assertEquals('home', strval($response->getBody()));
    }

    public function testGetEntitiesReturnsAListOfEntities(): void
    {
        $response = $this->client->testRequest('GET', 'mascots');
        $this->assertEquals(json_encode(self::MASCOTS_FIXTURE), strval($response->getBody()));
    }

    public function testGetEntityByIdReturnsTheSearchedEntity(): void
    {
        $mascotId = 1;
        
        $response = $this->client->testRequest('GET', "mascots/{$mascotId}");
        $foundMascotIndex = array_search(1, array_column(self::MASCOTS_FIXTURE, 'id'));
        $foundMascot = self::MASCOTS_FIXTURE[$foundMascotIndex];
        $this->assertEquals(json_encode($foundMascot), strval($response->getBody()));
    }
    
    public function testNotFoundGetEntityByIdReturnsEmpty(): void
    {
        $mascotId = 1000;

        $response = $this->client->testRequest('GET', "mascots/{$mascotId}");
        $this->assertEmpty(json_decode(strval($response->getBody())));
    }

    public function routeProvider(): \closure
    {
        $testControllerBuilder = new TestControllerBuilder();
        $app = $testControllerBuilder->buildOldControllerForTesting(__METHOD__);

        return function () use ($app){
            $app->get('/', function ($req, $res, $headerParams, $functionParams) {
                echo 'home';
            });

            $app->get('/mascots', function ($req, $res, $headerParams, $functionParams) {
                echo json_encode(self::MASCOTS_FIXTURE);
            });

            $app->get('/mascots/{id}', function ($req, $res, $headerParams, $functionParams) {
                $foundMascotIndex = array_search($headerParams->id, array_column(self::MASCOTS_FIXTURE, 'id'));
                if ($foundMascotIndex === false) {
                    echo json_encode([]);
                    return;
                }

                $foundMascot = self::MASCOTS_FIXTURE[$foundMascotIndex] ?? [];
                echo json_encode($foundMascot);
            });

            $app->dispatch();
        };
    }

    protected function tearDown(): void
    {
        unset($this->server);
    }
}
