<?php 

namespace Aloefflerj\YetAnotherController\Controller\Routes;

interface RouteInterface
{
    function __construct(string $uri, \closure $output, ?array $functionParams);
    
    function dispatch(): Route;

    static function getRoute(string $currentUri, array $routes, string $currentRequestMethod): string;

}