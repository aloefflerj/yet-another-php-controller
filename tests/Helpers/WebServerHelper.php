<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController\Tests\Helpers;

use Aloefflerj\YetAnotherController\Tests\Helpers\TestClient;

class WebServerHelper
{
    private ?int $localWebServerId = null;

    public function __construct(
        private string $host,
        private string $entryPoint,
        private string $testFileName,
        private $testMethod
    ) {
    }

    public function startWebServer()
    {
        if ($this->isRunning()) {
            return;
        }

        $this->launchWebServer();
        $this->waitUntilWebServerAcceptsRequests();
        $this->stopWebserverOnShutdown();
    }

    private function isRunning(): bool
    {
        return isset($this->localWebServerId);
    }

    private function launchWebServer(): void
    {
        $command = sprintf(
            'php -S %s -t %s >/dev/null 2>&1 & echo $!',
            $this->host,
            dirname(__DIR__, 1) . '/acceptance/entrypoint/' .  $this->entryPoint
        );

        $output = [];
        exec($command, $output);
        $this->localWebServerId = (int) $output[0];
    }

    private function waitUntilWebServerAcceptsRequests(): void
    {
        exec("composer wait-for-it {$this->host} -q", $output);
    }

    private function stopWebServerOnShutdown(): void
    {
        register_shutdown_function(function () {
            exec('kill ' . $this->localWebServerId);
        });
    }

    public function makeClient(): TestClient
    {
        return new TestClient([
            'base_uri' => 'http://' . $this->host,
            'http_errors' => false,
        ], $this->testFileName, $this->testMethod);
    }
}
