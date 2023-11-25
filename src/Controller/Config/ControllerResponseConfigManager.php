<?php

namespace Aloefflerj\YetAnotherController\Controller\Config;

use Psr\Http\Message\ResponseInterface;

class ControllerResponseConfigManager
{
    public function __construct(
        private ResponseInterface $response
    ) {
    }

    public function applyAllConfigurations(): void
    {
        $this->applyHeaders();
        $this->applyHttpStatusCode();
    }
    
    private function applyHeaders(): void
    {
        foreach ($this->response->getHeaders() as $headerKey => $headerValues) {
            $headerValues = implode(';', $headerValues);
            header("{$headerKey}: {$headerValues}");
        }
    }

    private function applyHttpStatusCode(): void
    {
        http_response_code($this->response->getStatusCode());
    }
}
