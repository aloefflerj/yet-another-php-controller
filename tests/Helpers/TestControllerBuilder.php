<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController\Tests\Helpers;

use Aloefflerj\YetAnotherController\Controller\BaseController;
use Aloefflerj\YetAnotherController\Controller\Controller;

class TestControllerBuilder
{
    public function __construct(private string $baseUri = '')
    {
    }

    public function buildControllerForTesting(string $methodThatCalledThisBuilder): Controller
    {
        $methodToRoute = $this->prepareRouteFromMethodPath($methodThatCalledThisBuilder);
        return new Controller($this->baseUri . $methodToRoute);
    }

    public function buildOldControllerForTesting(string $methodThatCalledThisBuilder): BaseController
    {
        $methodToRoute = $this->prepareRouteFromMethodPath($methodThatCalledThisBuilder);
        return new BaseController($methodToRoute);
    }

    private function prepareRouteFromMethodPath(string $methodPath): string
    {
        $classMethodChunks = explode('\\', $methodPath);
        $classMethod = end($classMethodChunks);

        return '/' . str_replace('::', '/', $classMethod);
    }
}
