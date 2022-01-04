<?php

namespace Aloefflerj\FedTheDog\Controller\Url;

use Aloefflerj\FedTheDog\Controller\Helpers\StringHelper;
use Aloefflerj\FedTheDog\Controller\Helpers\UrlHelper;
use stdClass;

class UrlHandler
// class UriHandler implements UriInterface
{
    use UrlHelper;
    use StringHelper;
    /**
     * Undocumented variable
     *
     * @var string
     */
    public string $url;

    // public function __construct(string $url) {
    //     /** @var string */
    //     $this->url = $url;
    // }

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
     * refactor => transform into traits
     */


     /**
      * Search for the route with passed url params
      *
      * @param string $currentUri
      * @param array $routes
      * @return string
      */
    public function routeWithUrlParams(string &$currentUri, array $routes): string
    {
        foreach($routes as $route) {
            $mappedRoute['equal'][] = $this->stringCompare($currentUri, $route->name);
            $mappedRoute['name'][] = $route->name;
        }

        $longest = '';
        $routeName = '';

        // echo "route: {$route->name}" substr_count($route->name, '/') . '<br>';
        // echo substr_count($currentUri, '/') . '<br>';
        foreach($mappedRoute['equal'] as $key => $routeChunk) {
            if(substr_count($mappedRoute['name'][$key], '/') === substr_count($currentUri, '/')) {
                if(empty($longest) || $routeChunk > $longest) {
                    $longest = $routeChunk;
                    $routeName = $mappedRoute['name'][$key];
                }
            }
        }
        return $routeName;
    }
}
