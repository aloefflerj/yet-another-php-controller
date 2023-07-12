<?php

namespace Aloefflerj\YetAnotherController\Controller\Exceptions;

use Aloefflerj\YetAnotherController\Controller\Router\Route;

class MoreThanOneRouteWasFound extends \Exception
{
    public function __construct(
        array $routes,
        string $requiredUrl,
        string $message = 'More than one route was found. Acessed url was ({{url}}). ' .
            'Routes found: ' .
            '{{routes}}'
    ) {
        $routesPath = array_map(
            fn (Route $route) => "'{$route->getUri()->getPath()}'",
            $routes
        );

        $routesPathMessage = implode(', ', $routesPath);
        
        $message = preg_replace(
            ['/\{\{url\}\}/', '/\{\{routes\}\}/'],
            [$requiredUrl, "({$routesPathMessage})"],
            $message
        );

        parent::__construct($message);
    }
}
