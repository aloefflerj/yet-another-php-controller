<?php

declare(strict_types=1);

namespace Aloefflerj\YetAnotherController\Tests\Helpers;

use Aloefflerj\YetAnotherController\Controller\BaseController;

class TestControllerBuilder
{
    public function buildOldController(string $methodThatCalledThisBuilder): BaseController
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
