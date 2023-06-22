<?php

namespace Aloefflerj\YetAnotherController\Controller\Url;

use Aloefflerj\YetAnotherController\Controller\Helpers\StringHelper;
use Aloefflerj\YetAnotherController\Controller\Helpers\UrlHelper;
use stdClass;

class UrlHandler
// class UriHandler implements UriInterface
{
    use UrlHelper;
    use StringHelper;

    public string $url;
    private ?string $path;

    public function getPath()
    {
        $this->path = $this->getUriPath();

        return $this->path;
    }


    /**
     * ||================================================================||
     *                          HELPER FUNCTIONS
     * ||================================================================||
     * 
     */


    public function routeWithUrlParams(string &$currentUri, array $routes): string
    {
        foreach ($routes as $route) {
            $mappedRoute['equal'][] = $this->stringCompare($currentUri, $route->name);
            $mappedRoute['name'][] = $route->name;
        }

        $longest = '';
        $routeName = '';

        foreach ($mappedRoute['equal'] as $key => $routeChunk) {
            if (substr_count($mappedRoute['name'][$key], '/') === substr_count($currentUri, '/')) {
                if (empty($longest) || $routeChunk > $longest) {
                    $longest = $routeChunk;
                    $routeName = $mappedRoute['name'][$key];
                }
            }
        }
        return $routeName;
    }
}
