<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController\Tests\Helpers;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class TestClient extends Client
{
    public function __construct(array $config = [], private string $testFileName, private string $testMethod)
    {
        parent::__construct($config);
    }

    public function testRequest(string $method, string $path = '', array $options = []): ResponseInterface
    {
        $uri = "/{$this->testFileName}/{$this->testMethod}/{$path}";
        return $this->request($method, $uri, $options);
    }
}
