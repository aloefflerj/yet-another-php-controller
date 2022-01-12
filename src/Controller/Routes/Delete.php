<?php

namespace Aloefflerj\YetAnotherController\Controller\Routes;

class Delete extends Route
{
    public function __construct(string $uri, \closure $output, ?array $functionParams)
    {
        parent::__construct($uri, $output, $functionParams);

        $this->verb         =  parent::getVerbName(__CLASS__);
        $this->urlParams    = $this->splitToParams($uri);
        $this->body         = file_get_contents('php://input', true); 
    }

    public static function getRoute($currentUri, $routes, $currentRequestMethod)
    {
        $currentRoute = self::$urlHandler->routeWithUrlParams($currentUri, $routes[$currentRequestMethod]);

        if (!$routes[$currentRequestMethod][$currentRoute]->urlParams) {
            $currentRoute = $currentUri;
        }

        return $currentRoute;
    }
}
