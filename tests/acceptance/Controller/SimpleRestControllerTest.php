<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController;

use Aloefflerj\YetAnotherController\Controller\Controller;
use Aloefflerj\YetAnotherController\Tests\Helpers\TestClient;
use Aloefflerj\YetAnotherController\Tests\Helpers\TestControllerBuilder;
use Aloefflerj\YetAnotherController\Tests\Helpers\WebServerHelper;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class SimpleRestControllerTest extends TestCase
{
    private ?WebServerHelper $server = null;
    private TestClient $client;
    private $mascotsFixture;

    protected function setUp(): void
    {
        $this->server = new WebServerHelper(
            'localhost:9100',
            '',
            (new \ReflectionClass($this))->getShortName(),
            'routesProvider'
        );
        $this->server->startWebServer();

        $this->client = $this->server->makeClient();

        $this->mascotsFixture = json_decode(file_get_contents(dirname(__DIR__, 2) . '/fixtures/mascots-fixture.json'));
    }

    public function testGetMascotsReturnsTheListOfMascots(): void
    {
        $response = $this->client->testRequest('GET', 'mascots');

        $this->assertEquals($this->mascotsFixture, json_decode(strval($response->getBody())));
    }

    public function routesProvider(): \closure
    {
        $controllerBuilder = new TestControllerBuilder('http://localhost:9100');
        $controller = $controllerBuilder->buildControllerForTesting(__METHOD__);

        return function () use ($controller) {
            $mascotsFixture = file_get_contents(dirname(__DIR__, 2) . '/fixtures/mascots-fixture.json');
            $mascotsFixture = json_decode($mascotsFixture);

            $controller->get('/', function (RequestInterface $_, ResponseInterface $response) {
                $response->getBody()->write('Welcome home');
                return $response;
            });

            $controller->get(
                '/mascots',
                function (
                    RequestInterface $_,
                    ResponseInterface $response,
                ) use ($mascotsFixture) {
                    $response->getBody()->write(
                        json_encode($mascotsFixture, JSON_PRETTY_PRINT)
                    );
                    return $response;
                },
            );

            $controller->get(
                '/mascots/{id}',
                function (
                    RequestInterface $_,
                    ResponseInterface $response,
                    \stdClass $args
                ) use ($mascotsFixture) {

                    $foundMascot = array_column($mascotsFixture, null, 'id')[$args->id] ?? new \stdClass();

                    $response->getBody()->write(
                        json_encode($foundMascot, JSON_PRETTY_PRINT)
                    );

                    return $response;
                },
            );

            /**
             * body tip (json):
             * {
             *  "id": 4,
             *	"lang": "java",
             *	"mascot": "duke"
             * }
             */
            $controller->post(
                '/mascots',
                function (
                    RequestInterface $request,
                    ResponseInterface $response,
                ) use ($mascotsFixture) {
                    $requestBodyContent = $request->getBody()->getContents();
                    $requestBody = json_decode($requestBodyContent);

                    $response->getBody()->write(
                        json_encode([...$mascotsFixture, $requestBody], JSON_PRETTY_PRINT)
                    );
                    return $response;
                },
            );

            /**
             * body tip (json):
             * {
             *  "id": 1,
             *	"lang": "php",
             *	"mascot": "elePHPant"
             * }
             */
            $controller->put(
                '/mascots/{id}',
                function (
                    RequestInterface $request,
                    ResponseInterface $response,
                    \stdClass $args
                ) use ($mascotsFixture) {
                    $requestBodyContent = $request->getBody()->getContents();
                    $requestBody = json_decode($requestBodyContent);

                    foreach ($mascotsFixture as $i => $mascot) {
                        if ($mascot->id === (int)$args->id) {
                            $mascotsFixture[$i] = $requestBody;
                        }
                    }

                    $response->getBody()->write(
                        json_encode($mascotsFixture, JSON_PRETTY_PRINT)
                    );
                    return $response;
                },
            );


            $controller->dispatch();
        };
    }
}
