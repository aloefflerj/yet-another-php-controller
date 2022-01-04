<?php

namespace Aloefflerj\FedTheDog\Controller\Routes;

use Aloefflerj\FedTheDog\Controller\Url\UrlHandler;

class Route
{
    public $name;
    public $output;
    public $verb;
    public $verbParams;
    public $functionParams;
    public static $urlHandler;

    /**
     * @param string $uri
     * @param \closure $output
     * @param array|null $functionParams
     */
    protected function __construct(string $uri, \closure $output, ?array $functionParams) {
        
        $this->name             = $uri;
        $this->output           = $output;
        $this->functionParams   = $functionParams;

        self::$urlHandler       = new UrlHandler();

    }

    public function dispatch()
    {
        $uri = self::$urlHandler->getUriPath();
        
        //Get the route params
        $callBackParams = $this->getParams($uri);

        if ($callBackParams === null) {
            $this->error = new \Exception("Error 404", 404);
            return $this;
        }

        // Get the output function
        $output = $this->output;
        
        if($this->verb === 'get') {
            $output('', '', $callBackParams);
            return $this;
        }

        $output('', '', $this->body, $callBackParams);

        return $this;
    }

    protected function getParams($currentUri, ?bool $overwriteArrayParams = false)
    {
        $urlParams = $this->verbParams;
        $params = $this->functionParams;

        if ($urlParams && !$params && !$overwriteArrayParams) {

            $urlParamsQty = count($urlParams);

            if (substr_count($currentUri, "/") - 1 !== $urlParamsQty) {
                return null;
            }

            $urlParamsArray = explode("/", $currentUri);
            $params = array_slice($urlParamsArray, -$urlParamsQty);

            $params = array_combine($urlParams, $params);
        }

        return (object)$params;
    }

    /**
     * Get the request method name
     *
     * @param string $verb
     * @return string
     */
    protected function getVerbName(string $verb): string
    {
        $separatedClassName = explode('\\', $verb);
        $className = end($separatedClassName);

        return strtolower($className);
    }

}
