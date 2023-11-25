<?php

namespace Aloefflerj\YetAnotherController\Controller\Config;

use Psr\Http\Message\ResponseInterface;

class ControllerConfigManagerFactory
{
    public function createResponseConfigManager(ResponseInterface $response): ControllerResponseConfigManager
    {
        return new ControllerResponseConfigManager($response);
    }
}