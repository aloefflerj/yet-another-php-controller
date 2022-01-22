<?php

namespace Aloefflerj\YetAnotherController\Controller\Http;

use Aloefflerj\YetAnotherController\Controller\PSR\UriInterface;

class Uri
// class Uri implements UriInterface
{

    private string $completeUri;
    private string $scheme;
    
    /**
     * @throws \Exception
     */
    public function __construct(string $uri = "") {
        if(!filter_var($uri, FILTER_VALIDATE_URL)) {
            throw new \Exception('This is not a valid uri');
        }       

        $this->completeUri = $uri;
        $this->splitUri();
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    private function splitUri(): void
    {
        $this->scheme = explode(':', $this->completeUri)[0];
    }
}