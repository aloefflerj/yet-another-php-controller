<?php

namespace Aloefflerj\YetAnotherController\Controller\Routes;

use Aloefflerj\YetAnotherController\Controller\Url\UrlHandler;

class Get extends Route
{
    public function __construct(string $uri, \closure $output, ?array $functionParams)
    {
        parent::__construct($uri, $output, $functionParams);

        $this->verb         =  parent::getVerbName(__CLASS__);
        $this->headerParams   = $this->splitToParams($uri);

        self::$urlHandler   = new UrlHandler();
    }

    /**
     * Split uri params into array
     *
     * @param string $uri
     * @return array|null
     */
    private function splitToParams(string $uri): ?array
    {
        if (strpos($uri, '{') !== false) {
            $headerParams = explode('{', $uri);
            $headerParams = str_replace(['}', '/'], '', $headerParams);
            array_shift($headerParams);
        }

        return $headerParams ?? null;
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
