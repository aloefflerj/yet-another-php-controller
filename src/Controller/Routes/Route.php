<?php

namespace Aloefflerj\YetAnotherController\Controller\Routes;

use Aloefflerj\YetAnotherController\Controller\Url\UrlHandler;

class Route
{
    public $name;
    public $output;
    public $method;
    public $headerParams;
    public $functionParams;
    public static $urlHandler;
    private $body;
    private $error;

    protected function __construct(string $uri, \closure $output, ?array $functionParams) {
        
        $this->name             = $uri;
        $this->output           = $output;
        $this->functionParams   = (object)$functionParams;

        self::$urlHandler       = new UrlHandler();

    }

    public function dispatch(): Route
    {
        $uri = self::$urlHandler->getUriPath();
        
        //Get the route params
        $callBackUrlParams = $this->getUrlParams($uri);

        if ($callBackUrlParams === null) {
            $this->error = new \Exception("Error 404", 404);
            return $this;
        }

        // Get the output function
        $output = $this->output;
        
        if($this->method === 'get') {
            $output('', '', $callBackUrlParams, $this->functionParams);
            return $this;
        }

        $output('', '', $this->body, $callBackUrlParams, $this->functionParams);

        return $this;
    }

    protected function getUrlParams($currentUri): \stdClass
    {
        $urlParamValues = null;
        $urlParams = $this->headerParams;

        if ($urlParams) {

            $urlParamsQty = count($urlParams);

            if (substr_count($this->name, "{") !== $urlParamsQty) {
                return null;
            }

            $urlParamsArray = explode("/", $currentUri);
            $urlParamValues = array_slice($urlParamsArray, -$urlParamsQty);

            $urlParamValues = array_combine($urlParams, $urlParamValues);
        }

        return (object)$urlParamValues;
    }

    protected function getMethodName(string $method): string
    {
        $separatedClassName = explode('\\', $method);
        $className = end($separatedClassName);

        return strtolower($className);
    }

    protected function splitToParams(string $uri): ?array
    {
        if (strpos($uri, '{') !== false) {
            $headerParams = explode('{', $uri);
            $headerParams = str_replace(['}', '/'], '', $headerParams);
            array_shift($headerParams);
        }

        return $headerParams ?? null;
    }

}
