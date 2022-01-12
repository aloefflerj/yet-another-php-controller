<?php

namespace Aloefflerj\YetAnotherController\Controller\Routes;

class Put extends Route
{
    public function __construct(string $uri, \closure $output, ?array $functionParams)
    {
        parent::__construct($uri, $output, $functionParams);

        $this->method         =  parent::getMethodName(__CLASS__);
        $this->headerParams    = $this->splitToParams($uri);
        $this->body         = file_get_contents('php://input', true); 
    }

    public static function getRoute($currentUri, $routes, $currentRequestMethod)
    {
        $currentRoute = self::$urlHandler->routeWithUrlParams($currentUri, $routes[$currentRequestMethod]);

        if (!$routes[$currentRequestMethod][$currentRoute]->headerParams) {
            $currentRoute = $currentUri;
        }

        return $currentRoute;
    }
}
